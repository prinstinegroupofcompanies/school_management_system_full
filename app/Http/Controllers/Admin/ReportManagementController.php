<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportTemplate;
use App\Models\ReportSchedule;
use App\Models\ReportExecution;
use App\Models\ReportSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReportManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_templates' => ReportTemplate::count(),
            'active_templates' => ReportTemplate::active()->count(),
            'total_schedules' => ReportSchedule::count(),
            'active_schedules' => ReportSchedule::active()->count(),
            'total_executions' => ReportExecution::count(),
            'completed_executions' => ReportExecution::completed()->count(),
            'failed_executions' => ReportExecution::failed()->count(),
            'running_executions' => ReportExecution::running()->count(),
            'total_subscriptions' => ReportSubscription::count(),
            'active_subscriptions' => ReportSubscription::active()->count(),
            'overdue_schedules' => ReportSchedule::overdue()->count(),
            'overdue_subscriptions' => ReportSubscription::overdue()->count()
        ];

        $recentExecutions = ReportExecution::with(['template', 'executedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $recentSchedules = ReportSchedule::with(['template', 'createdBy'])
            ->latest()
            ->limit(10)
            ->get();

        $executionTrends = ReportExecution::selectRaw('DATE(started_at) as date, COUNT(*) as count')
            ->where('started_at', '>=', now()->subDays(30))
            ->whereNotNull('started_at')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $templateStats = ReportTemplate::selectRaw('report_type, COUNT(*) as count')
            ->groupBy('report_type')
            ->get();

        $executionStats = ReportExecution::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('admin.reports.dashboard', compact(
            'stats', 'recentExecutions', 'recentSchedules', 
            'executionTrends', 'templateStats', 'executionStats'
        ));
    }

    // Templates Management
    public function templates(Request $request)
    {
        $query = ReportTemplate::query();

        if ($request->filled('report_type')) {
            $query->where('report_type', $request->report_type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        if ($request->filled('is_public')) {
            $query->where('is_public', $request->is_public === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('template_name', 'like', "%{$search}%")
                  ->orWhere('template_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $templates = $query->ordered()->paginate(20);

        $reportTypes = ReportTemplate::select('report_type')->distinct()->pluck('report_type');
        $categories = ReportTemplate::select('category')->distinct()->pluck('category');

        return view('admin.reports.templates', compact('templates', 'reportTypes', 'categories'));
    }

    public function createTemplate()
    {
        $reportTypes = [
            'academic' => 'Academic',
            'financial' => 'Financial',
            'administrative' => 'Administrative',
            'attendance' => 'Attendance',
            'performance' => 'Performance',
            'inventory' => 'Inventory',
            'health_safety' => 'Health & Safety',
            'visitor' => 'Visitor Management'
        ];

        $categories = [
            'student' => 'Student',
            'teacher' => 'Teacher',
            'staff' => 'Staff',
            'finance' => 'Finance',
            'academic' => 'Academic',
            'administrative' => 'Administrative',
            'general' => 'General'
        ];

        return view('admin.reports.templates.create', compact('reportTypes', 'categories'));
    }

    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'report_type' => 'required|string|in:academic,financial,administrative,attendance,performance,inventory,health_safety,visitor',
            'category' => 'required|string|in:student,teacher,staff,finance,academic,administrative,general',
            'data_sources' => 'required|array|min:1',
            'report_structure' => 'required|array',
            'filters' => 'nullable|array',
            'charts_config' => 'nullable|array',
            'export_formats' => 'required|array|min:1',
            'permissions' => 'nullable|array',
            'notification_settings' => 'nullable|array',
            'is_public' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            ReportTemplate::create($request->all());

            return redirect()->route('admin.reports.templates')
                ->with('success', 'Template created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create template: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showTemplate(ReportTemplate $template)
    {
        $template->load(['schedules', 'executions', 'subscriptions']);
        
        return view('admin.reports.templates.show', compact('template'));
    }

    public function editTemplate(ReportTemplate $template)
    {
        $reportTypes = [
            'academic' => 'Academic',
            'financial' => 'Financial',
            'administrative' => 'Administrative',
            'attendance' => 'Attendance',
            'performance' => 'Performance',
            'inventory' => 'Inventory',
            'health_safety' => 'Health & Safety',
            'visitor' => 'Visitor Management'
        ];

        $categories = [
            'student' => 'Student',
            'teacher' => 'Teacher',
            'staff' => 'Staff',
            'finance' => 'Finance',
            'academic' => 'Academic',
            'administrative' => 'Administrative',
            'general' => 'General'
        ];

        return view('admin.reports.templates.edit', compact('template', 'reportTypes', 'categories'));
    }

    public function updateTemplate(Request $request, ReportTemplate $template)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'report_type' => 'required|string|in:academic,financial,administrative,attendance,performance,inventory,health_safety,visitor',
            'category' => 'required|string|in:student,teacher,staff,finance,academic,administrative,general',
            'data_sources' => 'required|array|min:1',
            'report_structure' => 'required|array',
            'filters' => 'nullable|array',
            'charts_config' => 'nullable|array',
            'export_formats' => 'required|array|min:1',
            'permissions' => 'nullable|array',
            'notification_settings' => 'nullable|array',
            'is_public' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (!$template->canBeEdited()) {
                return redirect()->back()
                    ->with('error', 'This template cannot be edited as it has running executions.');
            }

            $template->update($request->all());

            return redirect()->route('admin.reports.templates')
                ->with('success', 'Template updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update template: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyTemplate(ReportTemplate $template)
    {
        try {
            if (!$template->canBeDeleted()) {
                return redirect()->back()
                    ->with('error', 'This template cannot be deleted as it has associated executions, schedules, or subscriptions.');
            }

            $template->delete();

            return redirect()->route('admin.reports.templates')
                ->with('success', 'Template deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete template: ' . $e->getMessage());
        }
    }

    public function toggleTemplateStatus(ReportTemplate $template)
    {
        try {
            if ($template->is_active) {
                $template->deactivate();
                $message = 'Template deactivated successfully.';
            } else {
                $template->activate();
                $message = 'Template activated successfully.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle template status: ' . $e->getMessage());
        }
    }

    // Schedules Management
    public function schedules(Request $request)
    {
        $query = ReportSchedule::with(['template', 'createdBy']);

        if ($request->filled('template_id')) {
            $query->where('template_id', $request->template_id);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        if ($request->filled('overdue')) {
            $query->overdue();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('schedule_name', 'like', "%{$search}%")
                  ->orWhereHas('template', function ($templateQuery) use ($search) {
                      $templateQuery->where('template_name', 'like', "%{$search}%");
                  });
            });
        }

        $schedules = $query->latest()->paginate(20);

        $templates = ReportTemplate::active()->select('id', 'template_name')->orderBy('template_name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom'];

        return view('admin.reports.schedules', compact('schedules', 'templates', 'frequencies'));
    }

    public function createSchedule()
    {
        $templates = ReportTemplate::active()->orderBy('template_name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom'];

        return view('admin.reports.schedules.create', compact('templates', 'frequencies'));
    }

    public function storeSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:report_templates,id',
            'schedule_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly,custom',
            'schedule_config' => 'required|array',
            'report_params' => 'nullable|array',
            'recipients' => 'required|array|min:1',
            'recipients.*.email' => 'required|email',
            'recipients.*.name' => 'nullable|string|max:255',
            'export_settings' => 'required|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $schedule = ReportSchedule::create(array_merge($request->all(), [
                'created_by' => auth()->id()
            ]));

            return redirect()->route('admin.reports.schedules')
                ->with('success', 'Schedule created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showSchedule(ReportSchedule $schedule)
    {
        $schedule->load(['template', 'createdBy', 'executions']);
        
        return view('admin.reports.schedules.show', compact('schedule'));
    }

    public function editSchedule(ReportSchedule $schedule)
    {
        $templates = ReportTemplate::active()->orderBy('template_name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom'];

        return view('admin.reports.schedules.edit', compact('schedule', 'templates', 'frequencies'));
    }

    public function updateSchedule(Request $request, ReportSchedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:report_templates,id',
            'schedule_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly,custom',
            'schedule_config' => 'required|array',
            'report_params' => 'nullable|array',
            'recipients' => 'required|array|min:1',
            'recipients.*.email' => 'required|email',
            'recipients.*.name' => 'nullable|string|max:255',
            'export_settings' => 'required|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (!$schedule->canBeEdited()) {
                return redirect()->back()
                    ->with('error', 'This schedule cannot be edited as it has running executions.');
            }

            $schedule->update($request->all());

            return redirect()->route('admin.reports.schedules')
                ->with('success', 'Schedule updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update schedule: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroySchedule(ReportSchedule $schedule)
    {
        try {
            if (!$schedule->canBeDeleted()) {
                return redirect()->back()
                    ->with('error', 'This schedule cannot be deleted as it has associated executions.');
            }

            $schedule->delete();

            return redirect()->route('admin.reports.schedules')
                ->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }

    public function toggleScheduleStatus(ReportSchedule $schedule)
    {
        try {
            if ($schedule->is_active) {
                $schedule->deactivate();
                $message = 'Schedule deactivated successfully.';
            } else {
                $schedule->activate();
                $message = 'Schedule activated successfully.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle schedule status: ' . $e->getMessage());
        }
    }

    public function executeSchedule(ReportSchedule $schedule)
    {
        try {
            if (!$schedule->canBeExecuted()) {
                return redirect()->back()
                    ->with('error', 'This schedule cannot be executed at this time.');
            }

            $execution = $schedule->execute();

            return redirect()->route('admin.reports.executions.show', $execution)
                ->with('success', 'Schedule executed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to execute schedule: ' . $e->getMessage());
        }
    }

    // Executions Management
    public function executions(Request $request)
    {
        $query = ReportExecution::with(['template', 'schedule', 'executedBy']);

        if ($request->filled('template_id')) {
            $query->where('template_id', $request->template_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('export_format')) {
            $query->where('export_format', $request->export_format);
        }

        if ($request->filled('executed_by')) {
            $query->where('executed_by', $request->executed_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('execution_id', 'like', "%{$search}%")
                  ->orWhereHas('template', function ($templateQuery) use ($search) {
                      $templateQuery->where('template_name', 'like', "%{$search}%");
                  });
            });
        }

        $executions = $query->latest()->paginate(20);

        $templates = ReportTemplate::select('id', 'template_name')->orderBy('template_name')->get();
        $users = User::select('id', 'name')->orderBy('name')->get();
        $statuses = ['pending', 'running', 'completed', 'failed', 'cancelled'];
        $exportFormats = ['PDF', 'Excel', 'CSV', 'HTML'];

        return view('admin.reports.executions', compact(
            'executions', 'templates', 'users', 'statuses', 'exportFormats'
        ));
    }

    public function showExecution(ReportExecution $execution)
    {
        $execution->load(['template', 'schedule', 'executedBy']);
        
        return view('admin.reports.executions.show', compact('execution'));
    }

    public function downloadExecution(ReportExecution $execution)
    {
        try {
            if (!$execution->can_be_downloaded) {
                return redirect()->back()
                    ->with('error', 'This execution cannot be downloaded.');
            }

            return response()->download(
                storage_path('app/public/' . $execution->file_path),
                $execution->file_name
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to download file: ' . $e->getMessage());
        }
    }

    public function cancelExecution(ReportExecution $execution)
    {
        try {
            if (!$execution->can_be_cancelled) {
                return redirect()->back()
                    ->with('error', 'This execution cannot be cancelled.');
            }

            $execution->cancel();

            return redirect()->back()
                ->with('success', 'Execution cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to cancel execution: ' . $e->getMessage());
        }
    }

    public function retryExecution(ReportExecution $execution)
    {
        try {
            if (!$execution->can_be_retried) {
                return redirect()->back()
                    ->with('error', 'This execution cannot be retried.');
            }

            $newExecution = $execution->retry();

            return redirect()->route('admin.reports.executions.show', $newExecution)
                ->with('success', 'Execution retried successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to retry execution: ' . $e->getMessage());
        }
    }

    public function destroyExecution(ReportExecution $execution)
    {
        try {
            if (!$execution->can_be_deleted) {
                return redirect()->back()
                    ->with('error', 'This execution cannot be deleted.');
            }

            $execution->cleanup();
            $execution->delete();

            return redirect()->route('admin.reports.executions')
                ->with('success', 'Execution deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete execution: ' . $e->getMessage());
        }
    }

    // Subscriptions Management
    public function subscriptions(Request $request)
    {
        $query = ReportSubscription::with(['template', 'user']);

        if ($request->filled('template_id')) {
            $query->where('template_id', $request->template_id);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        if ($request->filled('overdue')) {
            $query->overdue();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subscription_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('template', function ($templateQuery) use ($search) {
                      $templateQuery->where('template_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $subscriptions = $query->latest()->paginate(20);

        $templates = ReportTemplate::active()->select('id', 'template_name')->orderBy('template_name')->get();
        $users = User::select('id', 'name')->orderBy('name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];

        return view('admin.reports.subscriptions', compact(
            'subscriptions', 'templates', 'users', 'frequencies'
        ));
    }

    public function createSubscription()
    {
        $templates = ReportTemplate::active()->orderBy('template_name')->get();
        $users = User::orderBy('name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];

        return view('admin.reports.subscriptions.create', compact('templates', 'users', 'frequencies'));
    }

    public function storeSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:report_templates,id',
            'user_id' => 'required|exists:users,id',
            'subscription_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'report_params' => 'nullable|array',
            'filters' => 'nullable|array',
            'email' => 'required|email',
            'export_settings' => 'required|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            ReportSubscription::create($request->all());

            return redirect()->route('admin.reports.subscriptions')
                ->with('success', 'Subscription created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showSubscription(ReportSubscription $subscription)
    {
        $subscription->load(['template', 'user']);
        
        return view('admin.reports.subscriptions.show', compact('subscription'));
    }

    public function editSubscription(ReportSubscription $subscription)
    {
        $templates = ReportTemplate::active()->orderBy('template_name')->get();
        $users = User::orderBy('name')->get();
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];

        return view('admin.reports.subscriptions.edit', compact('subscription', 'templates', 'users', 'frequencies'));
    }

    public function updateSubscription(Request $request, ReportSubscription $subscription)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:report_templates,id',
            'user_id' => 'required|exists:users,id',
            'subscription_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'frequency' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'report_params' => 'nullable|array',
            'filters' => 'nullable|array',
            'email' => 'required|email',
            'export_settings' => 'required|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $subscription->update($request->all());

            return redirect()->route('admin.reports.subscriptions')
                ->with('success', 'Subscription updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update subscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroySubscription(ReportSubscription $subscription)
    {
        try {
            if (!$subscription->canBeDeleted()) {
                return redirect()->back()
                    ->with('error', 'This subscription cannot be deleted.');
            }

            $subscription->delete();

            return redirect()->route('admin.reports.subscriptions')
                ->with('success', 'Subscription deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete subscription: ' . $e->getMessage());
        }
    }

    public function toggleSubscriptionStatus(ReportSubscription $subscription)
    {
        try {
            if ($subscription->is_active) {
                $subscription->deactivate();
                $message = 'Subscription deactivated successfully.';
            } else {
                $subscription->activate();
                $message = 'Subscription activated successfully.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle subscription status: ' . $e->getMessage());
        }
    }

    public function sendSubscription(ReportSubscription $subscription)
    {
        try {
            if (!$subscription->canBeSent()) {
                return redirect()->back()
                    ->with('error', 'This subscription cannot be sent at this time.');
            }

            $subscription->send();

            return redirect()->back()
                ->with('success', 'Subscription sent successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send subscription: ' . $e->getMessage());
        }
    }

    // Statistics and Reports
    public function statistics()
    {
        $stats = [
            'total_templates' => ReportTemplate::count(),
            'active_templates' => ReportTemplate::active()->count(),
            'total_schedules' => ReportSchedule::count(),
            'active_schedules' => ReportSchedule::active()->count(),
            'total_executions' => ReportExecution::count(),
            'completed_executions' => ReportExecution::completed()->count(),
            'failed_executions' => ReportExecution::failed()->count(),
            'running_executions' => ReportExecution::running()->count(),
            'total_subscriptions' => ReportSubscription::count(),
            'active_subscriptions' => ReportSubscription::active()->count(),
            'overdue_schedules' => ReportSchedule::overdue()->count(),
            'overdue_subscriptions' => ReportSubscription::overdue()->count()
        ];

        $executionTrends = ReportExecution::selectRaw('DATE(started_at) as date, COUNT(*) as count')
            ->where('started_at', '>=', now()->subDays(30))
            ->whereNotNull('started_at')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $templateStats = ReportTemplate::selectRaw('report_type, COUNT(*) as count')
            ->groupBy('report_type')
            ->get();

        $executionStats = ReportExecution::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $exportFormatStats = ReportExecution::selectRaw('export_format, COUNT(*) as count')
            ->groupBy('export_format')
            ->get();

        return response()->json([
            'stats' => $stats,
            'execution_trends' => $executionTrends,
            'template_stats' => $templateStats,
            'execution_stats' => $executionStats,
            'export_format_stats' => $exportFormatStats
        ]);
    }
}