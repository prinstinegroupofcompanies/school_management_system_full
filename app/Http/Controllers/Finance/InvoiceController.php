<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\SystemSetting;
use App\Notifications\InvoiceNotification;
use App\Services\Fees\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PDF;

class InvoiceController extends Controller
{
    public function __construct()
    {
        // Auth for all; finance-only for management actions, not for student downloads
        $this->middleware(['auth']);
        $this->middleware('finance')->only(['create', 'bulkSend']);
    }

    public function create()
    {
        $classes = ClassRoom::all();
        $currentYear = (int) date('Y');
        $semesters = ['Semester 1', 'Semester 2'];
        return view('finance.invoices.create', compact('classes', 'currentYear', 'semesters'));
    }

    public function bulkSend(Request $request, StudentFeeService $studentFeeService)
    {
        $data = $request->validate([
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:class_rooms,id',
            'semester' => 'nullable|string|max:32',
            'year' => 'required|integer|min:2000|max:2100',
            'note' => 'nullable|string|max:1000',
        ]);

        $schoolName = SystemSetting::get('school_name', config('app.name'));
        $schoolLogo = SystemSetting::get('school_logo', '');
        $schoolLogoUrl = $schoolLogo ? asset('storage/' . $schoolLogo) : null;

        $bankDetails = [
            'bank_name' => SystemSetting::get('bank_name', ''),
            'bank_account' => SystemSetting::get('bank_account', ''),
        ];
        $mobileMoney = [
            'provider' => SystemSetting::get('mobile_money_provider', ''),
            'number' => SystemSetting::get('mobile_money_number', ''),
        ];

        $count = 0;
        foreach ($data['class_ids'] as $classId) {
            $students = Student::query()->where('class_id', $classId)->get();
            foreach ($students as $student) {
                $studentFee = $studentFeeService->createStudentFeeFor($student, $data['semester'] ?? null, (int) $data['year']);

                $invoiceNo = sprintf('INV-%s-%06d', $data['year'], $studentFee->id);
                $pdf = PDF::loadView('finance.invoices.pdf', [
                    'student' => $student,
                    'studentFee' => $studentFee,
                    'bankDetails' => $bankDetails,
                    'mobileMoney' => $mobileMoney,
                    'schoolName' => $schoolName,
                    'schoolLogoUrl' => $schoolLogoUrl,
                    'invoiceNo' => $invoiceNo,
                    'note' => $data['note'] ?? null,
                ]);

                $fileName = 'invoices/'.now()->format('Ymd_His')."_student_{$student->id}_fee_{$studentFee->id}.pdf";
                Storage::disk('public')->put($fileName, $pdf->output());

                // Try to notify; if mail isn't configured, log and continue
                try {
                    Notification::send($student->user, new InvoiceNotification($studentFee, $fileName));
                } catch (\Throwable $e) {
                    \Log::warning('Invoice notification failed', [
                        'student_id' => $student->id,
                        'fee_id' => $studentFee->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                $count++;
            }
        }

        return redirect()->route('finance.invoices.create')->with('success', "Invoices generated and notifications sent for {$count} students.");
    }

    public function download(StudentFee $studentFee)
    {
        // Authorization: allow Finance/Admin; or the owner student
        $user = Auth::user();
        $isFinanceOrAdmin = in_array($user?->user_type, ['finance', 'admin'], true);
        $isOwnerStudent = ($user?->user_type === 'student') && ($user->student?->id === $studentFee->student_id);
        abort_unless($isFinanceOrAdmin || $isOwnerStudent, 403, 'Access denied. Admin/Finance or the owning student required.');

        $schoolName = SystemSetting::get('school_name', config('app.name'));
        $schoolLogo = SystemSetting::get('school_logo', '');
        $schoolLogoUrl = $schoolLogo ? asset('storage/' . $schoolLogo) : null;

        $bankDetails = [
            'bank_name' => SystemSetting::get('bank_name', ''),
            'bank_account' => SystemSetting::get('bank_account', ''),
        ];
        $mobileMoney = [
            'provider' => SystemSetting::get('mobile_money_provider', ''),
            'number' => SystemSetting::get('mobile_money_number', ''),
        ];
        $student = $studentFee->student;
        $invoiceNo = sprintf('INV-%s-%06d', $studentFee->year, $studentFee->id);
        $pdf = PDF::loadView('finance.invoices.pdf', compact('student', 'studentFee', 'bankDetails', 'mobileMoney', 'schoolName', 'schoolLogoUrl', 'invoiceNo'));
        try {
            return $pdf->download('invoice_'.$studentFee->id.'.pdf');
        } catch (\Throwable $e) {
            return $pdf->stream('invoice_'.$studentFee->id.'.pdf');
        }
    }
}


