<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\VisitorLog;
use App\Models\VisitorCategory;
use App\Models\Student;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VisitorManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_visitors' => Visitor::count(),
            'active_visitors' => Visitor::notBlacklisted()->count(),
            'blacklisted_visitors' => Visitor::blacklisted()->count(),
            'visitors_today' => VisitorLog::today()->count(),
            'visitors_this_week' => VisitorLog::thisWeek()->count(),
            'visitors_this_month' => VisitorLog::thisMonth()->count(),
            'currently_checked_in' => VisitorLog::checkedIn()->count(),
            'overdue_visits' => VisitorLog::overdue()->count(),
            'total_categories' => VisitorCategory::active()->count(),
        ];

        $recentVisits = VisitorLog::with(['visitor', 'student', 'staff', 'checkedInBy'])
            ->orderBy('check_in_time', 'desc')
            ->limit(10)
            ->get();

        $currentlyCheckedIn = VisitorLog::with(['visitor', 'student', 'staff'])
            ->checkedIn()
            ->orderBy('check_in_time', 'desc')
            ->limit(10)
            ->get();

        $overdueVisits = VisitorLog::with(['visitor', 'student', 'staff'])
            ->overdue()
            ->orderBy('expected_check_out_time')
            ->limit(10)
            ->get();

        return view('admin.visitor-management.dashboard', compact(
            'stats', 'recentVisits', 'currentlyCheckedIn', 'overdueVisits'
        ));
    }

    // Visitor Management
    public function visitors(Request $request)
    {
        $query = Visitor::with('category');

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'blacklisted') {
                $query->blacklisted();
            } else {
                $query->notBlacklisted();
            }
        }

        if ($request->filled('organization')) {
            $query->byOrganization($request->organization);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('visitor_id', 'like', "%{$search}%");
            });
        }

        $visitors = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = VisitorCategory::active()->ordered()->get();

        return view('admin.visitor-management.visitors.index', compact('visitors', 'categories'));
    }

    public function createVisitor()
    {
        $categories = VisitorCategory::active()->ordered()->get();
        return view('admin.visitor-management.visitors.create', compact('categories'));
    }

    public function storeVisitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:50',
            'organization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'visitor_type' => 'required|in:parent,guardian,vendor,contractor,official,guest,other',
            'category_id' => 'nullable|exists:visitor_categories,id',
            'requires_escort' => 'boolean',
            'special_instructions' => 'nullable|string',
            'attachments' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['visitor_id'] = (new Visitor())->generateVisitorId();

        Visitor::create($data);

        return redirect()->route('admin.visitor-management.visitors')
            ->with('success', 'Visitor registered successfully.');
    }

    public function showVisitor(Visitor $visitor)
    {
        $visitor->load(['category', 'logs.checkedInBy', 'logs.checkedOutBy', 'logs.student', 'logs.staff']);
        return view('admin.visitor-management.visitors.show', compact('visitor'));
    }

    public function editVisitor(Visitor $visitor)
    {
        $categories = VisitorCategory::active()->ordered()->get();
        return view('admin.visitor-management.visitors.edit', compact('visitor', 'categories'));
    }

    public function updateVisitor(Request $request, Visitor $visitor)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'nullable|string|max:50',
            'organization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'visitor_type' => 'required|in:parent,guardian,vendor,contractor,official,guest,other',
            'category_id' => 'nullable|exists:visitor_categories,id',
            'requires_escort' => 'boolean',
            'special_instructions' => 'nullable|string',
            'attachments' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $visitor->update($request->all());

        return redirect()->route('admin.visitor-management.visitors')
            ->with('success', 'Visitor updated successfully.');
    }

    public function destroyVisitor(Visitor $visitor)
    {
        if (!$visitor->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'Cannot delete visitor with existing visit logs.');
        }

        $visitor->delete();
        return redirect()->route('admin.visitor-management.visitors')
            ->with('success', 'Visitor deleted successfully.');
    }

    public function blacklistVisitor(Request $request, Visitor $visitor)
    {
        $validator = Validator::make($request->all(), [
            'blacklist_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $visitor->blacklist($request->blacklist_reason);

        return redirect()->back()
            ->with('success', 'Visitor blacklisted successfully.');
    }

    public function removeFromBlacklist(Visitor $visitor)
    {
        $visitor->removeFromBlacklist();

        return redirect()->back()
            ->with('success', 'Visitor removed from blacklist successfully.');
    }

    // Visitor Logs Management
    public function logs(Request $request)
    {
        $query = VisitorLog::with(['visitor', 'student', 'staff', 'checkedInBy', 'checkedOutBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('visitor_id')) {
            $query->byVisitor($request->visitor_id);
        }

        if ($request->filled('student_id')) {
            $query->byStudent($request->student_id);
        }

        if ($request->filled('staff_id')) {
            $query->byStaff($request->staff_id);
        }

        if ($request->filled('purpose')) {
            $query->byPurpose($request->purpose);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $logs = $query->orderBy('check_in_time', 'desc')->paginate(15);
        $visitors = Visitor::notBlacklisted()->orderBy('first_name')->get();
        $students = Student::orderBy('first_name')->get();
        $staff = Staff::orderBy('first_name')->get();

        return view('admin.visitor-management.logs.index', compact('logs', 'visitors', 'students', 'staff'));
    }

    public function createLog()
    {
        $visitors = Visitor::notBlacklisted()->orderBy('first_name')->get();
        $students = Student::orderBy('first_name')->get();
        $staff = Staff::orderBy('first_name')->get();
        
        return view('admin.visitor-management.logs.create', compact('visitors', 'students', 'staff'));
    }

    public function storeLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitor_id' => 'required|exists:visitors,id',
            'student_id' => 'nullable|exists:students,id',
            'staff_id' => 'nullable|exists:staff,id',
            'purpose' => 'required|string|max:255',
            'purpose_details' => 'nullable|string',
            'destination' => 'required|string|max:255',
            'escort_name' => 'nullable|string|max:255',
            'escort_phone' => 'nullable|string|max:20',
            'expected_check_out_time' => 'nullable|date|after:now',
            'check_in_notes' => 'nullable|string',
            'vehicle_parked' => 'boolean',
            'vehicle_plate' => 'nullable|string|max:20',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_color' => 'nullable|string|max:50',
            'special_instructions' => 'nullable|string',
            'attachments' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['log_number'] = (new VisitorLog())->generateLogNumber();
        $data['checked_in_by'] = auth()->id();
        $data['check_in_time'] = now();

        VisitorLog::create($data);

        return redirect()->route('admin.visitor-management.logs')
            ->with('success', 'Visitor checked in successfully.');
    }

    public function showLog(VisitorLog $log)
    {
        $log->load(['visitor', 'student', 'staff', 'checkedInBy', 'checkedOutBy']);
        return view('admin.visitor-management.logs.show', compact('log'));
    }

    public function checkOut(Request $request, VisitorLog $log)
    {
        $validator = Validator::make($request->all(), [
            'check_out_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $log->checkOut(auth()->id(), $request->check_out_notes);

        return redirect()->back()
            ->with('success', 'Visitor checked out successfully.');
    }

    public function cancelLog(Request $request, VisitorLog $log)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $log->cancel($request->cancellation_notes);

        return redirect()->back()
            ->with('success', 'Visit cancelled successfully.');
    }

    // Visitor Categories Management
    public function categories(Request $request)
    {
        $query = VisitorCategory::query();

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->ordered()->paginate(15);

        return view('admin.visitor-management.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.visitor-management.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:visitor_categories,code',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'requires_approval' => 'boolean',
            'requires_escort' => 'boolean',
            'max_visits_per_day' => 'nullable|integer|min:1',
            'allowed_areas' => 'nullable|array',
            'restricted_areas' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['code'] = $data['code'] ?: (new VisitorCategory())->generateCode();

        VisitorCategory::create($data);

        return redirect()->route('admin.visitor-management.categories')
            ->with('success', 'Visitor category created successfully.');
    }

    public function showCategory(VisitorCategory $category)
    {
        $category->load('visitors');
        return view('admin.visitor-management.categories.show', compact('category'));
    }

    public function editCategory(VisitorCategory $category)
    {
        return view('admin.visitor-management.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, VisitorCategory $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:visitor_categories,code,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'requires_approval' => 'boolean',
            'requires_escort' => 'boolean',
            'max_visits_per_day' => 'nullable|integer|min:1',
            'allowed_areas' => 'nullable|array',
            'restricted_areas' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category->update($request->all());

        return redirect()->route('admin.visitor-management.categories')
            ->with('success', 'Visitor category updated successfully.');
    }

    public function destroyCategory(VisitorCategory $category)
    {
        if (!$category->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with associated visitors.');
        }

        $category->delete();
        return redirect()->route('admin.visitor-management.categories')
            ->with('success', 'Visitor category deleted successfully.');
    }

    public function toggleCategoryStatus(VisitorCategory $category)
    {
        $category->is_active ? $category->deactivate() : $category->activate();
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Visitor category {$status} successfully.");
    }

    // Statistics and Reports
    public function statistics()
    {
        $visitorStats = [
            'by_type' => Visitor::select('visitor_type', DB::raw('count(*) as count'))
                ->groupBy('visitor_type')
                ->get(),
            'by_category' => Visitor::select('visitor_categories.name as category_name', DB::raw('count(visitors.id) as count'))
                ->leftJoin('visitor_categories', 'visitors.category_id', '=', 'visitor_categories.id')
                ->groupBy('visitor_categories.name')
                ->get(),
            'by_month' => Visitor::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        $logStats = [
            'by_status' => VisitorLog::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'by_purpose' => VisitorLog::select('purpose', DB::raw('count(*) as count'))
                ->groupBy('purpose')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'by_destination' => VisitorLog::select('destination', DB::raw('count(*) as count'))
                ->groupBy('destination')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'by_month' => VisitorLog::select(DB::raw('DATE_FORMAT(check_in_time, "%Y-%m") as month'), DB::raw('count(*) as count'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        return view('admin.visitor-management.statistics', compact('visitorStats', 'logStats'));
    }
}
