<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthIncident;
use App\Models\HealthRecord;
use App\Models\SafetyCheck;
use App\Models\EmergencyContact;
use App\Models\Student;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HealthSafetyController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // Health Incidents Management
    public function incidents(Request $request)
    {
        $query = HealthIncident::with(['student', 'staff', 'reportedBy']);

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('severity')) {
            $query->bySeverity($request->severity);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $incidents = $query->orderBy('incident_date', 'desc')->paginate(15);

        return view('admin.health-safety.incidents.index', compact('incidents'));
    }

    public function createIncident()
    {
        $students = Student::orderBy('first_name')->get();
        $staff = Staff::orderBy('first_name')->get();
        
        return view('admin.health-safety.incidents.create', compact('students', 'staff'));
    }

    public function storeIncident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'nullable|exists:students,id',
            'staff_id' => 'nullable|exists:staff,id',
            'incident_type' => 'required|string|max:255',
            'severity' => 'required|in:minor,moderate,major,critical',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'symptoms' => 'nullable|string',
            'actions_taken' => 'nullable|string',
            'medical_treatment' => 'nullable|string',
            'follow_up_required' => 'nullable|boolean',
            'incident_date' => 'required|date',
            'witnesses' => 'nullable|array',
            'attachments' => 'nullable|array',
            'parent_notified' => 'boolean',
            'authorities_notified' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['incident_number'] = (new HealthIncident())->generateIncidentNumber();
        $data['reported_by'] = auth()->id();
        $data['reported_date'] = now();

        HealthIncident::create($data);

        return redirect()->route('admin.health-safety.incidents.index')
    ->with('success', 'Health incident reported successfully.');
    }

    public function showIncident(HealthIncident $incident)
    {
        $incident->load(['student', 'staff', 'reportedBy']);
        return view('admin.health-safety.incidents.show', compact('incident'));
    }

    public function editIncident(HealthIncident $incident)
    {
        $students = Student::orderBy('first_name')->get();
        $staff = Staff::orderBy('first_name')->get();
        
        return view('admin.health-safety.incidents.edit', compact('incident', 'students', 'staff'));
    }

    public function updateIncident(Request $request, HealthIncident $incident)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'nullable|exists:students,id',
            'staff_id' => 'nullable|exists:staff,id',
            'incident_type' => 'required|string|max:255',
            'severity' => 'required|in:minor,moderate,major,critical',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'symptoms' => 'nullable|string',
            'actions_taken' => 'nullable|string',
            'medical_treatment' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'status' => 'required|in:reported,investigating,resolved,closed',
            'incident_date' => 'required|date',
            'investigation_notes' => 'nullable|string',
            'prevention_measures' => 'nullable|string',
            'witnesses' => 'nullable|array',
            'attachments' => 'nullable|array',
            'parent_notified' => 'boolean',
            'authorities_notified' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        if ($request->status === 'resolved' && !$incident->resolved_date) {
            $data['resolved_date'] = now();
        }

        $incident->update($data);

        return redirect()->route('admin.health-safety.incidents')
            ->with('success', 'Health incident updated successfully.');
    }

    public function destroyIncident(HealthIncident $incident)
    {
        $incident->delete();
        return redirect()->route('admin.health-safety.incidents')
            ->with('success', 'Health incident deleted successfully.');
    }

    // Health Records Management
    public function records(Request $request)
    {
        $query = HealthRecord::with(['student', 'recordedBy']);

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $records = $query->orderBy('record_date', 'desc')->paginate(15);
        $students = Student::orderBy('first_name')->get();

        return view('admin.health-safety.records.index', compact('records', 'students'));
    }

    public function createRecord()
    {
        $students = Student::orderBy('first_name')->get();
        return view('admin.health-safety.records.create', compact('students'));
    }

    public function storeRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'record_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'record_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:record_date',
            'health_provider' => 'nullable|string|max:255',
            'provider_contact' => 'nullable|string|max:255',
            'medical_notes' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
            'emergency_instructions' => 'nullable|string',
            'vital_signs' => 'nullable|array',
            'attachments' => 'nullable|array',
            'is_confidential' => 'boolean',
            'requires_follow_up' => 'boolean',
            'follow_up_date' => 'nullable|date|after:record_date',
            'follow_up_notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['recorded_by'] = auth()->id();

        HealthRecord::create($data);

        return redirect()->route('admin.health-safety.records.index')
            ->with('success', 'Health record created successfully.');
    }

    public function showRecord(HealthRecord $record)
    {
        $record->load(['student', 'recordedBy']);
        return view('admin.health-safety.records.show', compact('record'));
    }

    // Safety Checks Management
    public function safetyChecks(Request $request)
    {
        $query = SafetyCheck::with(['checkedBy', 'approvedBy']);

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('area')) {
            $query->byArea($request->area);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $checks = $query->orderBy('check_date', 'desc')->paginate(15);

        return view('admin.health-safety.safety-checks.index', compact('checks'));
    }

    public function createSafetyCheck()
    {
        return view('admin.health-safety.safety-checks.create');
    }

    public function storeSafetyCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'check_type' => 'required|string|max:255',
            'area_checked' => 'required|string|max:255',
            'check_description' => 'required|string',
            'status' => 'required|in:passed,failed,needs_attention,critical',
            'findings' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'check_date' => 'required|date',
            'next_check_date' => 'nullable|date|after:check_date',
            'checklist_items' => 'nullable|array',
            'photos' => 'nullable|array',
            'requires_follow_up' => 'boolean',
            'follow_up_date' => 'nullable|date|after:check_date',
            'follow_up_notes' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['check_number'] = (new SafetyCheck())->generateCheckNumber();
        $data['checked_by'] = auth()->id();

        SafetyCheck::create($data);

        return redirect()->route('admin.health-safety.safety-checks.index')
    ->with('success', 'Safety check recorded successfully.');
    }

    public function showSafetyCheck(SafetyCheck $check)
    {
        $check->load(['checkedBy', 'approvedBy']);
        return view('admin.health-safety.safety-checks.show', compact('check'));
    }

    public function approveSafetyCheck(Request $request, SafetyCheck $check)
    {
        $validator = Validator::make($request->all(), [
            'approval_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $check->approve(auth()->id(), $request->approval_notes);

        return redirect()->back()
            ->with('success', 'Safety check approved successfully.');
    }

    // Emergency Contacts Management
    public function emergencyContacts(Request $request)
    {
        $query = EmergencyContact::query();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('organization')) {
            $query->byOrganization($request->organization);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $contacts = $query->orderBy('priority', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.health-safety.emergency-contacts.index', compact('contacts'));
    }

    public function createEmergencyContact()
    {
        return view('admin.health-safety.emergency-contacts.create');
    }

    public function storeEmergencyContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'contact_type' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'services_provided' => 'nullable|string',
            'specialization' => 'nullable|string',
            'availability' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        EmergencyContact::create($request->all());

        return redirect()->route('admin.health-safety.emergency-contacts')
            ->with('success', 'Emergency contact created successfully.');
    }

    public function showEmergencyContact(EmergencyContact $contact)
    {
        return view('admin.health-safety.emergency-contacts.show', compact('contact'));
    }

    public function editEmergencyContact(EmergencyContact $contact)
    {
        return view('admin.health-safety.emergency-contacts.edit', compact('contact'));
    }

    public function updateEmergencyContact(Request $request, EmergencyContact $contact)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'contact_type' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'services_provided' => 'nullable|string',
            'specialization' => 'nullable|string',
            'availability' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contact->update($request->all());

        return redirect()->route('admin.health-safety.emergency-contacts')
            ->with('success', 'Emergency contact updated successfully.');
    }

    public function destroyEmergencyContact(EmergencyContact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.health-safety.emergency-contacts')
            ->with('success', 'Emergency contact deleted successfully.');
    }

    public function toggleEmergencyContactStatus(EmergencyContact $contact)
    {
        $contact->is_active ? $contact->deactivate() : $contact->activate();
        
        $status = $contact->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Emergency contact {$status} successfully.");
    }

    // Dashboard and Statistics
    public function dashboard()
    {
        $stats = [
            'total_incidents' => HealthIncident::count(),
            'critical_incidents' => HealthIncident::critical()->count(),
            'unresolved_incidents' => HealthIncident::unresolved()->count(),
            'total_records' => HealthRecord::count(),
            'expiring_records' => HealthRecord::expiringSoon()->count(),
            'total_safety_checks' => SafetyCheck::count(),
            'failed_checks' => SafetyCheck::failed()->count(),
            'overdue_checks' => SafetyCheck::overdue()->count(),
            'total_contacts' => EmergencyContact::count(),
            'active_contacts' => EmergencyContact::active()->count(),
        ];

        $recentIncidents = HealthIncident::with(['student', 'staff'])
            ->orderBy('incident_date', 'desc')
            ->limit(5)
            ->get();

        $upcomingChecks = SafetyCheck::with('checkedBy')
            ->upcoming()
            ->orderBy('next_check_date')
            ->limit(5)
            ->get();

        $expiringRecords = HealthRecord::with('student')
            ->expiringSoon()
            ->orderBy('expiry_date')
            ->limit(5)
            ->get();

        return view('admin.health-safety.dashboard', compact(
            'stats', 'recentIncidents', 'upcomingChecks', 'expiringRecords'
        ));
    }

    public function statistics()
    {
        $incidentStats = [
            'by_type' => HealthIncident::select('incident_type', DB::raw('count(*) as count'))
                ->groupBy('incident_type')
                ->get(),
            'by_severity' => HealthIncident::select('severity', DB::raw('count(*) as count'))
                ->groupBy('severity')
                ->get(),
            'by_month' => HealthIncident::select(DB::raw('DATE_FORMAT(incident_date, "%Y-%m") as month'), DB::raw('count(*) as count'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        $safetyStats = [
            'by_type' => SafetyCheck::select('check_type', DB::raw('count(*) as count'))
                ->groupBy('check_type')
                ->get(),
            'by_status' => SafetyCheck::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
        ];

        $recordStats = [
            'by_type' => HealthRecord::select('record_type', DB::raw('count(*) as count'))
                ->groupBy('record_type')
                ->get(),
            'by_status' => HealthRecord::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
        ];

        return view('admin.health-safety.statistics', compact(
            'incidentStats', 'safetyStats', 'recordStats'
        ));
    }
}
