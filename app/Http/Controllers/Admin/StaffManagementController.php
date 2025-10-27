<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffPerformance;
use App\Models\StaffSchedule;
use App\Models\Payroll;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role']);
        
        // Allow finance officers to access payroll methods
        $allowedRoutes = ['payroll', 'showPayroll', 'createPayroll', 'storePayroll', 'editPayroll', 'updatePayroll', 'destroyPayroll'];
        
        $this->middleware(function ($request, $next) use ($allowedRoutes) {
            $user = auth()->user();
            
            if (in_array($request->route()->getActionMethod(), $allowedRoutes)) {
                if (in_array($user->user_type, ['admin', 'finance'])) {
                    return $next($request);
                }
                abort(403, 'Access denied.');
            } elseif ($user->user_type !== 'admin') {
                abort(403, 'Admin privileges required.');
            }
            
            return $next($request);
        })->except(array_merge($allowedRoutes, ['index']));
    }

    // Staff Management Dashboard
    public function index(Request $request)
    {
        try {
            $query = Staff::with(['user', 'department', 'designation']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('employee_id', 'like', "%{$search}%");
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        $staff = $query->paginate(15);
        $departments = Department::all();

        // Dashboard statistics
        try {
            $stats = [
                'total_staff' => Staff::count(),
                'active_staff' => Staff::where('employment_status', 'active')->count(),
                'departments' => Department::count(),
                'pending_performance' => StaffPerformance::where('status', 'draft')->count(),
                'upcoming_schedules' => StaffSchedule::upcoming()->count(),
                'pending_payroll' => Payroll::where('status', 'pending')->count()
            ];
        } catch (\Exception $e) {
            \Log::error('Stats calculation error: ' . $e->getMessage());
            $stats = [
                'total_staff' => Staff::count(),
                'active_staff' => 0,
                'departments' => 0,
                'pending_performance' => 0,
                'upcoming_schedules' => 0,
                'pending_payroll' => 0
            ];
        }

        return view('admin.staff.index', compact('staff', 'departments', 'stats'));
        } catch (\Exception $e) {
            \Log::error('StaffManagementController index error: ' . $e->getMessage());
            $staff = collect()->paginate(15);
            $departments = collect();
            $stats = [
                'total_staff' => 0,
                'active_staff' => 0,
                'departments' => 0,
                'pending_performance' => 0,
                'upcoming_schedules' => 0,
                'pending_payroll' => 0
            ];
            return view('admin.staff.index', compact('staff', 'departments', 'stats'));
        }
    }

    // Create new staff member
    public function create()
    {
        $departments = Department::all();
        $designations = Designation::all();
        return view('admin.staff.create', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'required|string|unique:staff',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'qualification' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'employment_status' => 'nullable|in:active,inactive,suspended',
            'salary_currency' => 'nullable|in:LRD,USD',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_branch' => 'nullable|string|max:100',
            'tax_identification_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'staff',
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Create staff record
            $staff = Staff::create([
                'user_id' => $user->id,
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'qualification' => $request->qualification,
                'joining_date' => $request->joining_date,
                'basic_salary' => $request->basic_salary,
                'employment_type' => $request->employment_type ?? 'full_time',
                'employment_status' => $request->employment_status ?? 'active',
                'salary_currency' => $request->salary_currency ?? 'LRD',
                'bank_account_number' => $request->bank_account_number,
                'bank_branch' => $request->bank_branch,
                'tax_identification_number' => $request->tax_identification_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'emergency_contact_address' => $request->emergency_contact_address,
            ]);

            DB::commit();
            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create staff member.'])->withInput();
        }
    }

    // Show staff member details
    public function show(Staff $staff)
    {
        $staff->load(['user', 'department', 'designation', 'performances', 'schedules', 'payrolls']);
        return view('admin.staff.show', compact('staff'));
    }

    // Edit staff member
    public function edit(Staff $staff)
    {
        $departments = Department::all();
        $designations = Designation::all();
        $staff->load('user');
        return view('admin.staff.edit', compact('staff', 'departments', 'designations'));
    }

    // Update staff member
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->user_id,
            'employee_id' => 'required|string|unique:staff,employee_id,' . $staff->id,
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'qualification' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'employment_status' => 'nullable|in:active,inactive,suspended',
            'salary_currency' => 'nullable|in:LRD,USD',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_branch' => 'nullable|string|max:100',
            'tax_identification_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Update user account
            $staff->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|string|min:8|confirmed',
                ]);
                $staff->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Update staff record
            $staff->update([
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'designation_id' => $request->designation_id,
                'qualification' => $request->qualification,
                'joining_date' => $request->joining_date,
                'basic_salary' => $request->basic_salary,
                'employment_type' => $request->employment_type ?? 'full_time',
                'employment_status' => $request->employment_status ?? 'active',
                'salary_currency' => $request->salary_currency ?? 'LRD',
                'bank_account_number' => $request->bank_account_number,
                'bank_branch' => $request->bank_branch,
                'tax_identification_number' => $request->tax_identification_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'emergency_contact_address' => $request->emergency_contact_address,
            ]);

            DB::commit();
            return redirect()->route('admin.staff.show', $staff)
                ->with('success', 'Staff member updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update staff member.'])->withInput();
        }
    }

    // Delete staff member
    public function destroy(Staff $staff)
    {
        DB::beginTransaction();
        try {
            // Delete associated records first
            $staff->performances()->delete();
            $staff->schedules()->delete();
            $staff->payrolls()->delete();
            
            // Delete staff record
            $staff->delete();
            
            // Delete user account
            $staff->user->delete();

            DB::commit();
            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete staff member.']);
        }
    }

    // Staff Performance Management
    public function performance(Request $request)
    {
        $query = StaffPerformance::with(['staff.user', 'evaluator']);

        if ($request->filled('period')) {
            $query->where('evaluation_period', $request->period);
        }

        if ($request->filled('rating')) {
            $query->where('performance_rating', $request->rating);
        }

        $performances = $query->paginate(15);
        $periods = StaffPerformance::distinct()->pluck('evaluation_period');

        return view('admin.staff.performance', compact('performances', 'periods'));
    }

    public function createPerformance()
    {
        $staff = Staff::with('user')->where('employment_status', 'active')->get();
        $evaluators = User::where('user_type', 'admin')->orWhere('user_type', 'teacher')->get();
        // Pass empty errors array to prevent undefined variable errors
        $errors = new \Illuminate\Support\ViewErrorBag();
        return view('admin.staff.create-performance', compact('staff', 'evaluators', 'errors'));
    }

    public function storePerformance(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'evaluator_id' => 'required|exists:users,id',
            'evaluation_period' => 'required|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'punctuality' => 'required|integer|min:1|max:10',
            'work_quality' => 'required|integer|min:1|max:10',
            'teamwork' => 'required|integer|min:1|max:10',
            'communication' => 'required|integer|min:1|max:10',
            'initiative' => 'required|integer|min:1|max:10',
            'problem_solving' => 'required|integer|min:1|max:10',
            'performance_rating' => 'required|in:excellent,good,satisfactory,needs_improvement,unsatisfactory',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals' => 'nullable|string',
            'comments' => 'nullable|string',
            'status' => 'required|in:draft,submitted,reviewed,approved,disputed'
        ]);

        // Calculate overall score
        $scores = [
            $request->punctuality,
            $request->work_quality,
            $request->teamwork,
            $request->communication,
            $request->initiative,
            $request->problem_solving
        ];
        $overallScore = array_sum($scores) / count($scores);

        $performance = StaffPerformance::create([
            'staff_id' => $request->staff_id,
            'evaluator_id' => $request->evaluator_id,
            'evaluation_period' => $request->evaluation_period,
            'evaluation_date' => now(),
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'punctuality' => $request->punctuality,
            'work_quality' => $request->work_quality,
            'teamwork' => $request->teamwork,
            'communication' => $request->communication,
            'initiative' => $request->initiative,
            'problem_solving' => $request->problem_solving,
            'overall_score' => round($overallScore, 2),
            'performance_rating' => $request->performance_rating,
            'strengths' => $request->strengths,
            'areas_for_improvement' => $request->areas_for_improvement,
            'goals' => $request->goals,
            'comments' => $request->comments,
            'status' => $request->status
        ]);

        return redirect()->route('admin.staff.performance')
            ->with('success', 'Performance evaluation created successfully.');
    }

    public function showPerformance(StaffPerformance $performance)
    {
        $performance->load(['staff.user', 'evaluator']);
        return view('admin.staff.show-performance', compact('performance'));
    }

    public function editPerformance(StaffPerformance $performance)
    {
        $staff = Staff::with('user')->where('employment_status', 'active')->get();
        $evaluators = User::where('user_type', 'admin')->orWhere('user_type', 'teacher')->get();
        return view('admin.staff.edit-performance', compact('performance', 'staff', 'evaluators'));
    }

    public function updatePerformance(Request $request, StaffPerformance $performance)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'evaluator_id' => 'required|exists:users,id',
            'evaluation_period' => 'required|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'punctuality' => 'required|integer|min:1|max:10',
            'work_quality' => 'required|integer|min:1|max:10',
            'teamwork' => 'required|integer|min:1|max:10',
            'communication' => 'required|integer|min:1|max:10',
            'initiative' => 'required|integer|min:1|max:10',
            'problem_solving' => 'required|integer|min:1|max:10',
            'performance_rating' => 'required|in:excellent,good,satisfactory,needs_improvement,unsatisfactory',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals' => 'nullable|string',
            'comments' => 'nullable|string',
            'status' => 'required|in:draft,submitted,reviewed,approved,disputed'
        ]);

        // Calculate overall score
        $scores = [
            $request->punctuality,
            $request->work_quality,
            $request->teamwork,
            $request->communication,
            $request->initiative,
            $request->problem_solving
        ];
        $overallScore = array_sum($scores) / count($scores);

        $performance->update([
            'staff_id' => $request->staff_id,
            'evaluator_id' => $request->evaluator_id,
            'evaluation_period' => $request->evaluation_period,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'punctuality' => $request->punctuality,
            'work_quality' => $request->work_quality,
            'teamwork' => $request->teamwork,
            'communication' => $request->communication,
            'initiative' => $request->initiative,
            'problem_solving' => $request->problem_solving,
            'overall_score' => round($overallScore, 2),
            'performance_rating' => $request->performance_rating,
            'strengths' => $request->strengths,
            'areas_for_improvement' => $request->areas_for_improvement,
            'goals' => $request->goals,
            'comments' => $request->comments,
            'status' => $request->status
        ]);

        return redirect()->route('admin.staff.performance')
            ->with('success', 'Performance evaluation updated successfully.');
    }

    public function destroyPerformance(StaffPerformance $performance)
    {
        $performance->delete();
        return redirect()->route('admin.staff.performance')
                        ->with('success', 'Performance evaluation deleted successfully.');
    }

    // Staff Scheduling
    public function schedules(Request $request)
    {
        $query = StaffSchedule::with(['staff.user', 'assignedBy']);

        if ($request->filled('date')) {
            $query->where('schedule_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('schedule_date')->paginate(15);
        $staff = Staff::with('user')->where('employment_status', 'active')->get();

        return view('admin.staff.schedules', compact('schedules', 'staff'));
    }

    public function createSchedule()
    {
        $staff = Staff::with('user')->where('employment_status', 'active')->get();
        // Pass empty errors array to prevent undefined variable errors
        $errors = new \Illuminate\Support\ViewErrorBag();
        return view('admin.staff.create-schedule', compact('staff', 'errors'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'schedule_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'shift_type' => 'required|in:morning,afternoon,evening,night',
            'work_location' => 'nullable|string|max:255',
            'duties' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,confirmed,pending'
        ]);

        // Calculate duration
        $start = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $end = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
        
        // Handle overnight shifts
        if ($end < $start) {
            $end->addDay();
        }
        
        $durationHours = $start->diffInHours($end);

        StaffSchedule::create([
            'staff_id' => $request->staff_id,
            'schedule_date' => $request->schedule_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'shift_type' => $request->shift_type,
            'work_location' => $request->work_location,
            'duties' => $request->duties,
            'notes' => $request->notes,
            'assigned_by' => auth()->id(),
            'status' => $request->status
        ]);

        return redirect()->route('admin.staff.schedules')
            ->with('success', 'Schedule created successfully.');
    }

    // Payroll Management
    public function payroll(Request $request)
    {
        $query = Payroll::with(['staff.user', 'processedBy', 'academicPeriod']);

        if ($request->filled('academic_period_id')) {
            $period = \App\Models\AcademicPeriod::find($request->academic_period_id);
            if ($period) {
                $query->whereBetween('pay_period_start', [$period->start_date, $period->end_date]);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->whereHas('staff', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        $payrolls = $query->orderBy('pay_period_start', 'desc')->paginate(15);
        $staff = Staff::with('user')->where('employment_status', 'active')->get();
        $departments = Department::all();
        
        // Get academic periods for filter
        $payPeriods = \App\Models\AcademicPeriod::currentYear()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->map(function($name) {
                return $name . ' Period';
            });

        // Calculate stats
        $stats = [
            'total_payroll' => Payroll::sum('net_pay'),
            'processed' => Payroll::where('status', 'processed')->count(),
            'pending' => Payroll::where('status', 'pending')->count(),
            'overdue' => Payroll::where('status', 'pending')
                ->where('pay_date', '<', now())
                ->count()
        ];

        return view('admin.staff.payroll', compact('payrolls', 'staff', 'departments', 'payPeriods', 'stats'));
    }

    public function createPayroll()
    {
        $staff = Staff::with('user')->where('employment_status', 'active')->get();
        $departments = Department::all();
        $academicPeriods = \App\Models\AcademicPeriod::currentYear()->orderBy('name')->get();
        // Pass empty errors array to prevent undefined variable errors
        $errors = new \Illuminate\Support\ViewErrorBag();
        return view('admin.staff.create-payroll', compact('staff', 'departments', 'academicPeriods', 'errors'));
    }

    public function storePayroll(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'pay_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'hours_worked' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'income_tax' => 'nullable|numeric|min:0',
            'social_security' => 'nullable|numeric|min:0',
            'pension_contribution' => 'nullable|numeric|min:0',
            'health_insurance' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,check,mobile_money',
            'status' => 'required|in:draft,pending,approved,processed,paid,cancelled',
            'notes' => 'nullable|string',
            'days_worked' => 'nullable|integer|min:0',
            'days_absent' => 'nullable|integer|min:0',
            'days_leave' => 'nullable|integer|min:0',
            'leave_deduction' => 'nullable|numeric|min:0'
        ]);

        // Get academic period details
        $academicPeriod = \App\Models\AcademicPeriod::find($request->academic_period_id);

        // Calculate gross salary (basic + allowances + overtime + bonus)
        $grossSalary = $request->basic_salary + 
                      ($request->housing_allowance ?? 0) + 
                      ($request->transport_allowance ?? 0) + 
                      ($request->meal_allowance ?? 0) + 
                      ($request->medical_allowance ?? 0) + 
                      ($request->bonus ?? 0) + 
                      ($request->commission ?? 0) + 
                      ($request->other_allowances ?? 0) +
                      (($request->overtime_hours ?? 0) * ($request->overtime_rate ?? 0));

        // Calculate total deductions
        $totalDeductions = ($request->income_tax ?? 0) + 
                          ($request->social_security ?? 0) + 
                          ($request->pension_contribution ?? 0) + 
                          ($request->health_insurance ?? 0) + 
                          ($request->loan_deduction ?? 0) + 
                          ($request->advance_deduction ?? 0) + 
                          ($request->other_deductions ?? 0);

        $netSalary = $grossSalary - $totalDeductions;

        $payroll = Payroll::create([
            'staff_id' => $request->staff_id,
            'academic_period_id' => $request->academic_period_id,
            'pay_period_start' => $academicPeriod->start_date,
            'pay_period_end' => $academicPeriod->end_date,
            'pay_date' => $request->pay_date,
            'basic_salary' => $request->basic_salary,
            'hourly_rate' => $request->hourly_rate ?? 0,
            'hours_worked' => $request->hours_worked ?? 0,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'overtime_rate' => $request->overtime_rate ?? 0,
            'housing_allowance' => $request->housing_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'meal_allowance' => $request->meal_allowance ?? 0,
            'medical_allowance' => $request->medical_allowance ?? 0,
            'bonus' => $request->bonus ?? 0,
            'commission' => $request->commission ?? 0,
            'other_allowances' => $request->other_allowances ?? 0,
            'income_tax' => $request->income_tax ?? 0,
            'social_security' => $request->social_security ?? 0,
            'pension_contribution' => $request->pension_contribution ?? 0,
            'health_insurance' => $request->health_insurance ?? 0,
            'loan_deduction' => $request->loan_deduction ?? 0,
            'advance_deduction' => $request->advance_deduction ?? 0,
            'other_deductions' => $request->other_deductions ?? 0,
            'gross_salary' => $grossSalary,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'payment_method' => $request->payment_method,
            'status' => $request->status,
            'notes' => $request->notes,
            'payroll_number' => 'PAY' . date('Ymd') . str_pad(Payroll::count() + 1, 4, '0', STR_PAD_LEFT),
            'processed_by' => auth()->id(),
            'days_worked' => $request->days_worked ?? 0,
            'days_absent' => $request->days_absent ?? 0,
            'days_leave' => $request->days_leave ?? 0,
            'leave_deduction' => $request->leave_deduction ?? 0
        ]);

        return redirect()->route('admin.staff.payroll')
            ->with('success', 'Payroll record created successfully.');
    }

    // Reports
    public function reports()
    {
        $stats = [
            'total_staff' => Staff::count(),
            'active_staff' => Staff::where('employment_status', 'active')->count(),
            'average_performance' => StaffPerformance::avg('overall_score'),
            'total_payroll_this_month' => Payroll::whereRaw('strftime("%m", pay_date) = ?', [now()->format('m')])
                ->whereRaw('strftime("%Y", pay_date) = ?', [now()->format('Y')])
                ->sum('net_pay'),
            'department_breakdown' => Staff::with('department')
                ->select('department_id', DB::raw('count(*) as count'))
                ->groupBy('department_id')
                ->get(),
            'performance_breakdown' => StaffPerformance::select('performance_rating', DB::raw('count(*) as count'))
                ->groupBy('performance_rating')
                ->get()
        ];

        return view('admin.staff.reports', compact('stats'));
    }

    // Schedule Management Methods
    public function editSchedule(StaffSchedule $schedule)
    {
        $staff = Staff::all();
        return view('admin.staff.edit-schedule', compact('schedule', 'staff'));
    }

    public function updateSchedule(Request $request, StaffSchedule $schedule)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'schedule_type' => 'required|in:regular,overtime,holiday',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.staff.schedules')
                        ->with('success', 'Schedule updated successfully.');
    }

    public function showSchedule(StaffSchedule $schedule)
    {
        $schedule->load(['staff.user', 'assignedBy']);
        return view('admin.staff.show-schedule', compact('schedule'));
    }

    public function destroySchedule(StaffSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.staff.schedules')
                        ->with('success', 'Schedule deleted successfully.');
    }

    // Payroll Management Methods
    public function editPayroll(Payroll $payroll)
    {
        $staff = Staff::all();
        $academicPeriods = AcademicPeriod::all();
        return view('admin.staff.edit-payroll', compact('payroll', 'staff', 'academicPeriods'));
    }

    public function updatePayroll(Request $request, Payroll $payroll)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,processed,paid',
        ]);

        $data = $request->all();
        
        // Calculate net pay
        $grossPay = $data['basic_salary'] + ($data['allowances'] ?? 0) + ($data['bonus'] ?? 0) + 
                   (($data['overtime_hours'] ?? 0) * ($data['overtime_rate'] ?? 0));
        $totalDeductions = ($data['deductions'] ?? 0) + ($data['tax_deduction'] ?? 0);
        $data['gross_pay'] = $grossPay;
        $data['net_pay'] = $grossPay - $totalDeductions;

        $payroll->update($data);

        return redirect()->route('admin.staff.payroll')
                        ->with('success', 'Payroll updated successfully.');
    }

    public function showPayroll(Payroll $payroll)
    {
        $payroll->load(['staff.user', 'processedBy', 'academicPeriod']);
        return view('admin.staff.show-payroll', compact('payroll'));
    }

    public function printPayroll(Payroll $payroll)
    {
        $payroll->load(['staff.user', 'processedBy', 'academicPeriod']);
        return view('admin.staff.print-payroll', compact('payroll'));
    }

    public function destroyPayroll(Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            return redirect()->route('admin.staff.payroll')
                            ->with('error', 'Cannot delete payroll that has been paid.');
        }

        $payroll->delete();
        return redirect()->route('admin.staff.payroll')
                        ->with('success', 'Payroll deleted successfully.');
    }
}
