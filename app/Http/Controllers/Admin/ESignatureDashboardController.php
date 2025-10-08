<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ESignature;
use App\Models\ESignatureTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ESignatureDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_signatures' => ESignature::count(),
            'pending_signatures' => ESignature::where('status', 'pending')->count(),
            'verified_signatures' => ESignature::where('status', 'verified')->count(),
            'active_templates' => ESignatureTemplate::where('is_active', true)->count(),
            'signed_signatures' => ESignature::where('status', 'signed')->count(),
            'expired_signatures' => ESignature::where('status', 'expired')->count(),
            'revoked_signatures' => ESignature::where('status', 'revoked')->count(),
        ];

        // Get recent signatures
        $recentSignatures = ESignature::with(['user', 'template'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.e-signatures.dashboard', compact('stats', 'recentSignatures'));
    }
}
