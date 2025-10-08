<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ESignature;
use App\Models\ESignatureTemplate;
use App\Models\ESignatureApproval;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ESignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_signatures' => ESignature::count(),
            'pending_signatures' => ESignature::pending()->count(),
            'signed_signatures' => ESignature::signed()->count(),
            'verified_signatures' => ESignature::verified()->count(),
            'expired_signatures' => ESignature::expired()->count(),
            'revoked_signatures' => ESignature::where('status', 'revoked')->count(),
            'total_templates' => ESignatureTemplate::count(),
            'active_templates' => ESignatureTemplate::active()->count(),
            'pending_approvals' => ESignatureApproval::pending()->count(),
            'overdue_approvals' => ESignatureApproval::overdue()->count()
        ];

        $recentSignatures = ESignature::with(['user', 'approvals.approver'])
            ->latest()
            ->limit(10)
            ->get();

        $recentApprovals = ESignatureApproval::with(['signature.user', 'approver'])
            ->latest()
            ->limit(10)
            ->get();

        $signatureTrends = ESignature::selectRaw('DATE(signed_at) as date, COUNT(*) as count')
            ->where('signed_at', '>=', now()->subDays(30))
            ->whereNotNull('signed_at')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $documentTypeStats = ESignature::selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->get();

        $approvalStats = ESignatureApproval::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('admin.e-signatures.dashboard', compact(
            'stats', 'recentSignatures', 'recentApprovals', 
            'signatureTrends', 'documentTypeStats', 'approvalStats'
        ));
    }

    // Signatures Management
    public function signatures(Request $request)
    {
        $query = ESignature::with(['user', 'approvals.approver']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('signature_type')) {
            $query->where('signature_type', $request->signature_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('signed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('signed_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('signature_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $signatures = $query->latest()->paginate(20);

        $users = User::select('id', 'name')->orderBy('name')->get();
        $documentTypes = ESignature::select('document_type')->distinct()->pluck('document_type');
        $signatureTypes = ESignature::select('signature_type')->distinct()->pluck('signature_type');

        return view('admin.e-signatures.signatures', compact(
            'signatures', 'users', 'documentTypes', 'signatureTypes'
        ));
    }

    public function create()
    {
        $users = User::select('id', 'name')->orderBy('name')->get();
        $templates = ESignatureTemplate::active()->orderBy('template_name')->get();
        
        $documentTypes = [
            'lesson_plan' => 'Lesson Plan',
            'grade_submission' => 'Grade Submission',
            'monthly_report' => 'Monthly Report',
            'transcript' => 'Transcript',
            'admission_application' => 'Admission Application'
        ];
        
        $signatureTypes = [
            'digital' => 'Digital Signature',
            'biometric' => 'Biometric Signature',
            'certificate' => 'Certificate-based'
        ];

        return view('admin.e-signatures.create', compact(
            'users', 'templates', 'documentTypes', 'signatureTypes'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'template_id' => 'nullable|exists:e_signature_templates,id',
            'document_type' => 'required|string|max:255',
            'signature_type' => 'required|string|in:digital,biometric,certificate',
            'document_title' => 'required|string|max:255',
            'document_content' => 'nullable|string',
            'expiry_date' => 'required|date|after:today',
            'requires_witness' => 'boolean',
            'witness_name' => 'nullable|string|max:255',
            'witness_email' => 'nullable|email|max:255',
            'notification_emails' => 'nullable|array',
            'notification_emails.*' => 'email|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $signature = ESignature::create([
                'user_id' => $request->user_id,
                'template_id' => $request->template_id,
                'document_type' => $request->document_type,
                'signature_type' => $request->signature_type,
                'document_title' => $request->document_title,
                'document_content' => $request->document_content,
                'expiry_date' => $request->expiry_date,
                'requires_witness' => $request->boolean('requires_witness'),
                'witness_name' => $request->witness_name,
                'witness_email' => $request->witness_email,
                'notification_emails' => $request->notification_emails,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            // Create approval workflow if template is provided
            if ($request->template_id) {
                $template = ESignatureTemplate::find($request->template_id);
                if ($template && $template->approval_workflow) {
                    $signature->createApprovalWorkflow($template->approval_workflow);
                }
            }

            DB::commit();

            return redirect()->route('admin.e-signatures.show', $signature)
                ->with('success', 'Signature request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to create signature request: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showSignature(ESignature $signature)
    {
        $signature->load(['user', 'approvals.approver', 'approvals.delegatedTo']);
        
        return view('admin.e-signatures.show', compact('signature'));
    }

    public function verifySignature(Request $request, ESignature $signature)
    {
        $validator = Validator::make($request->all(), [
            'verification_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $signature->verify($request->verification_notes);

            return redirect()->route('admin.e-signatures.show', $signature)
                ->with('success', 'Signature verified successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to verify signature: ' . $e->getMessage());
        }
    }

    public function revokeSignature(Request $request, ESignature $signature)
    {
        $validator = Validator::make($request->all(), [
            'revocation_reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $signature->revoke($request->revocation_reason);

            return redirect()->route('admin.e-signatures.show', $signature)
                ->with('success', 'Signature revoked successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to revoke signature: ' . $e->getMessage());
        }
    }

    public function destroySignature(ESignature $signature)
    {
        try {
            if (!$signature->canBeDeleted()) {
                return redirect()->back()
                    ->with('error', 'This signature cannot be deleted.');
            }

            $signature->delete();

            return redirect()->route('admin.e-signatures.signatures')
                ->with('success', 'Signature deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete signature: ' . $e->getMessage());
        }
    }

    // Templates Management
    public function templates(Request $request)
    {
        $query = ESignatureTemplate::query();

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
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

        $documentTypes = ESignatureTemplate::select('document_type')->distinct()->pluck('document_type');

        return view('admin.e-signatures.templates', compact('templates', 'documentTypes'));
    }

    public function createTemplate()
    {
        $documentTypes = [
            'lesson_plan' => 'Lesson Plan',
            'grade_submission' => 'Grade Submission',
            'monthly_report' => 'Monthly Report',
            'transcript' => 'Transcript',
            'admission_application' => 'Admission Application'
        ];

        return view('admin.e-signatures.templates.create', compact('documentTypes'));
    }

    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type' => 'required|string|in:lesson_plan,grade_submission,monthly_report,transcript,admission_application',
            'signature_fields' => 'required|array|min:1',
            'signature_fields.*' => 'required|string|max:255',
            'approval_workflow' => 'nullable|array',
            'signature_requirements' => 'required|array',
            'expiry_days' => 'required|integer|min:1|max:365',
            'requires_witness' => 'boolean',
            'requires_notarization' => 'boolean',
            'notification_settings' => 'nullable|array',
            'security_settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            ESignatureTemplate::create($request->all());

            return redirect()->route('admin.e-signatures.templates')
                ->with('success', 'Template created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create template: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showTemplate(ESignatureTemplate $template)
    {
        $template->load('signatures');
        
        return view('admin.e-signatures.templates.show', compact('template'));
    }

    public function editTemplate(ESignatureTemplate $template)
    {
        $documentTypes = [
            'lesson_plan' => 'Lesson Plan',
            'grade_submission' => 'Grade Submission',
            'monthly_report' => 'Monthly Report',
            'transcript' => 'Transcript',
            'admission_application' => 'Admission Application'
        ];

        return view('admin.e-signatures.templates.edit', compact('template', 'documentTypes'));
    }

    public function updateTemplate(Request $request, ESignatureTemplate $template)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type' => 'required|string|in:lesson_plan,grade_submission,monthly_report,transcript,admission_application',
            'signature_fields' => 'required|array|min:1',
            'signature_fields.*' => 'required|string|max:255',
            'approval_workflow' => 'nullable|array',
            'signature_requirements' => 'required|array',
            'expiry_days' => 'required|integer|min:1|max:365',
            'requires_witness' => 'boolean',
            'requires_notarization' => 'boolean',
            'notification_settings' => 'nullable|array',
            'security_settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (!$template->canBeEdited()) {
                return redirect()->back()
                    ->with('error', 'This template cannot be edited as it has active signatures.');
            }

            $template->update($request->all());

            return redirect()->route('admin.e-signatures.templates')
                ->with('success', 'Template updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update template: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyTemplate(ESignatureTemplate $template)
    {
        try {
            if (!$template->canBeDeleted()) {
                return redirect()->back()
                    ->with('error', 'This template cannot be deleted as it has associated signatures.');
            }

            $template->delete();

            return redirect()->route('admin.e-signatures.templates')
                ->with('success', 'Template deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete template: ' . $e->getMessage());
        }
    }

    public function toggleTemplateStatus(ESignatureTemplate $template)
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

    // Approvals Management
    public function approvals(Request $request)
    {
        $query = ESignatureApproval::with(['signature.user', 'approver', 'delegatedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_level')) {
            $query->where('approval_level', $request->approval_level);
        }

        if ($request->filled('approver_id')) {
            $query->where('approver_id', $request->approver_id);
        }

        if ($request->filled('overdue')) {
            $query->overdue();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('signature', function ($sigQuery) use ($search) {
                    $sigQuery->where('signature_id', 'like', "%{$search}%");
                })->orWhereHas('approver', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $approvals = $query->latest()->paginate(20);

        $users = User::select('id', 'name')->orderBy('name')->get();
        $approvalLevels = ESignatureApproval::select('approval_level')->distinct()->pluck('approval_level');

        return view('admin.e-signatures.approvals', compact(
            'approvals', 'users', 'approvalLevels'
        ));
    }

    public function showApproval(ESignatureApproval $approval)
    {
        $approval->load(['signature.user', 'approver', 'delegatedTo']);
        
        return view('admin.e-signatures.approvals.show', compact('approval'));
    }

    public function approveSignature(Request $request, ESignatureApproval $approval)
    {
        $validator = Validator::make($request->all(), [
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (!$approval->canBeApproved()) {
                return redirect()->back()
                    ->with('error', 'This approval cannot be approved.');
            }

            $approval->approve(
                $request->approval_notes,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('admin.e-signatures.approvals')
                ->with('success', 'Signature approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve signature: ' . $e->getMessage());
        }
    }

    public function rejectSignature(Request $request, ESignatureApproval $approval)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (!$approval->canBeRejected()) {
                return redirect()->back()
                    ->with('error', 'This approval cannot be rejected.');
            }

            $approval->reject(
                $request->rejection_reason,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('admin.e-signatures.approvals')
                ->with('success', 'Signature rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject signature: ' . $e->getMessage());
        }
    }

    public function delegateApproval(Request $request, ESignatureApproval $approval)
    {
        $validator = Validator::make($request->all(), [
            'delegated_to' => 'required|exists:users,id',
            'delegation_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (!$approval->canBeDelegated()) {
                return redirect()->back()
                    ->with('error', 'This approval cannot be delegated.');
            }

            $approval->delegate(
                $request->delegated_to,
                $request->delegation_notes,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('admin.e-signatures.approvals')
                ->with('success', 'Approval delegated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delegate approval: ' . $e->getMessage());
        }
    }

    // Statistics and Reports
    public function statistics()
    {
        $stats = [
            'total_signatures' => ESignature::count(),
            'pending_signatures' => ESignature::pending()->count(),
            'signed_signatures' => ESignature::signed()->count(),
            'verified_signatures' => ESignature::verified()->count(),
            'expired_signatures' => ESignature::expired()->count(),
            'revoked_signatures' => ESignature::where('status', 'revoked')->count(),
            'total_templates' => ESignatureTemplate::count(),
            'active_templates' => ESignatureTemplate::active()->count(),
            'pending_approvals' => ESignatureApproval::pending()->count(),
            'overdue_approvals' => ESignatureApproval::overdue()->count()
        ];

        $signatureTrends = ESignature::selectRaw('DATE(signed_at) as date, COUNT(*) as count')
            ->where('signed_at', '>=', now()->subDays(30))
            ->whereNotNull('signed_at')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $documentTypeStats = ESignature::selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->get();

        $approvalStats = ESignatureApproval::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $signatureTypeStats = ESignature::selectRaw('signature_type, COUNT(*) as count')
            ->groupBy('signature_type')
            ->get();

        return response()->json([
            'stats' => $stats,
            'signature_trends' => $signatureTrends,
            'document_type_stats' => $documentTypeStats,
            'approval_stats' => $approvalStats,
            'signature_type_stats' => $signatureTypeStats
        ]);
    }
}