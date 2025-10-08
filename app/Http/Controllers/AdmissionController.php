<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdmissionController extends Controller
{
    /**
     * Show the admission application form
     */
    public function showApplicationForm()
    {
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();
        $requiredDocuments = $this->getRequiredDocuments();
        
        return view('admission.application-form', compact('classes', 'requiredDocuments'));
    }

    /**
     * Store a new admission application
     */
    public function storeApplication(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'nationality' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'applying_class_id' => 'required|exists:class_rooms,id',
            'parent_first_name' => 'required|string|max:255',
            'parent_last_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'parent_address' => 'required|string',
            'relationship_to_student' => 'required|string|max:255',
            'application_fee' => 'required|numeric|min:0',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // Generate application number
            $applicationNumber = $this->generateApplicationNumber();

            // Handle document uploads
            $documentPaths = [];
            $submittedDocuments = [];
            
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $key => $file) {
                    $documentType = $request->input("document_types.{$key}");
                    if ($documentType) {
                        $path = $file->store("admission-documents/{$applicationNumber}", 'public');
                        $documentPaths[$documentType] = $path;
                        $submittedDocuments[] = $documentType;
                    }
                }
            }

            // Create the application
            $application = AdmissionApplication::create([
                'application_number' => $applicationNumber,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'applying_class_id' => $request->applying_class_id,
                'previous_school' => $request->previous_school,
                'previous_class' => $request->previous_class,
                'previous_gpa' => $request->previous_gpa,
                'academic_achievements' => $request->academic_achievements,
                'parent_first_name' => $request->parent_first_name,
                'parent_last_name' => $request->parent_last_name,
                'parent_middle_name' => $request->parent_middle_name,
                'parent_phone' => $request->parent_phone,
                'parent_email' => $request->parent_email,
                'parent_address' => $request->parent_address,
                'parent_occupation' => $request->parent_occupation,
                'parent_employer' => $request->parent_employer,
                'relationship_to_student' => $request->relationship_to_student,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'application_fee' => $request->application_fee,
                'application_fee_paid' => $request->boolean('application_fee_paid'),
                'application_fee_payment_date' => $request->application_fee_payment_date,
                'payment_reference' => $request->payment_reference,
                'special_needs' => $request->special_needs,
                'medical_conditions' => $request->medical_conditions,
                'allergies' => $request->allergies,
                'medications' => $request->medications,
                'extracurricular_activities' => $request->extracurricular_activities,
                'hobbies' => $request->hobbies,
                'why_choose_school' => $request->why_choose_school,
                'required_documents' => array_keys($this->getRequiredDocuments()),
                'submitted_documents' => $submittedDocuments,
                'document_paths' => $documentPaths,
                'application_date' => now()->toDateString(),
                'status' => 'draft',
                'metadata' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'submitted_via' => 'public_form'
                ]
            ]);

            DB::commit();

            return redirect()->route('admission.success', $application->application_number)
                           ->with('success', 'Application submitted successfully! Your application number is: ' . $applicationNumber);

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                        ->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    /**
     * Show application success page
     */
    public function showSuccess($applicationNumber)
    {
        $application = AdmissionApplication::where('application_number', $applicationNumber)->firstOrFail();
        
        return view('admission.success', compact('application'));
    }

    /**
     * Check application status
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'application_number' => 'required|string',
            'parent_phone' => 'required|string'
        ]);

        $application = AdmissionApplication::where('application_number', $request->application_number)
                                         ->where('parent_phone', $request->parent_phone)
                                         ->first();

        if (!$application) {
            return back()->with('error', 'Application not found. Please check your application number and phone number.');
        }

        return view('admission.status', compact('application'));
    }

    /**
     * Show application status check form
     */
    public function showStatusForm()
    {
        return view('admission.check-status');
    }

    /**
     * Generate unique application number
     */
    private function generateApplicationNumber()
    {
        $year = now()->year;
        $prefix = 'APP';
        
        $lastApplication = AdmissionApplication::whereYear('application_date', $year)
            ->where('application_number', 'like', $prefix . $year . '%')
            ->orderBy('application_number', 'desc')
            ->first();

        if ($lastApplication) {
            $lastNumber = (int) substr($lastApplication->application_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get required documents list
     */
    private function getRequiredDocuments()
    {
        return [
            'birth_certificate' => 'Birth Certificate',
            'previous_report_card' => 'Previous School Report Card',
            'transfer_letter' => 'Transfer Letter',
            'passport_photo' => 'Passport Photograph',
            'medical_certificate' => 'Medical Certificate',
            'parent_id' => 'Parent/Guardian ID',
            'proof_of_residence' => 'Proof of Residence'
        ];
    }

    /**
     * Download application form (PDF)
     */
    public function downloadApplication($applicationNumber)
    {
        $application = AdmissionApplication::where('application_number', $applicationNumber)->firstOrFail();
        
        // Generate PDF using DomPDF or similar
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admission.application-pdf', compact('application'));
        
        return $pdf->download("admission-application-{$applicationNumber}.pdf");
    }

    /**
     * Show admission requirements and information
     */
    public function showRequirements()
    {
        return view('admission.requirements');
    }

    /**
     * Show admission calendar and deadlines
     */
    public function showCalendar()
    {
        return view('admission.calendar');
    }
}
