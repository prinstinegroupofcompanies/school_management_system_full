<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display signature settings
     */
    public function signature()
    {
        $user = Auth::user();
        return view('admin.settings.signature', compact('user'));
    }

    /**
     * Update signature
     */
    public function updateSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old signature if exists
        if ($user->signature && Storage::exists($user->signature)) {
            Storage::delete($user->signature);
        }

        // Store new signature
        $signaturePath = $request->file('signature')->store('signatures', 'public');
        
        $user->update([
            'signature' => $signaturePath
        ]);

        return redirect()->route('admin.settings.signature')
            ->with('success', 'Signature updated successfully!');
    }

    /**
     * Remove signature
     */
    public function removeSignature()
    {
        $user = Auth::user();

        if ($user->signature && Storage::exists($user->signature)) {
            Storage::delete($user->signature);
        }

        $user->update([
            'signature' => null
        ]);

        return redirect()->route('admin.settings.signature')
            ->with('success', 'Signature removed successfully!');
    }
}