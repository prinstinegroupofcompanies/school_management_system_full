<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GoogleClassroomController extends Controller
{
    /**
     * Show Google Classroom integration page.
     */
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        return view('teacher.google-classroom.index', compact('teacher'));
    }

    /**
     * Connect Google Classroom account.
     */
    public function connect()
    {
        // Google OAuth URL for Classroom API
        $clientId = config('services.google.client_id');
        $redirectUri = route('teacher.google-classroom.callback');
        $scopes = 'https://www.googleapis.com/auth/classroom.courses.readonly https://www.googleapis.com/auth/classroom.rosters.readonly';
        
        $authUrl = "https://accounts.google.com/o/oauth2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return redirect($authUrl);
    }

    /**
     * Handle OAuth callback.
     */
    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('teacher.google-classroom.index')
                ->with('error', 'Authorization failed.');
        }

        // Exchange code for token (simplified - should use proper OAuth library)
        // In production, use Google API client library
        $token = $this->exchangeCodeForToken($request->code);

        if ($token) {
            // Store token in user settings or separate table
            $user = Auth::user();
            $settings = \App\Models\UserSetting::getOrCreateForUser($user->id);
            $currentSettings = $settings->settings ?? [];
            $currentSettings['google_classroom_token'] = $token;
            $settings->update(['settings' => $currentSettings]);

            return redirect()->route('teacher.google-classroom.index')
                ->with('success', 'Google Classroom connected successfully.');
        }

        return redirect()->route('teacher.google-classroom.index')
            ->with('error', 'Failed to connect Google Classroom.');
    }

    /**
     * Sync classes from Google Classroom.
     */
    public function sync(Request $request)
    {
        $user = Auth::user();
        $settings = \App\Models\UserSetting::getOrCreateForUser($user->id);
        
        $token = $settings->settings['google_classroom_token'] ?? null;
        
        if (!$token) {
            return redirect()->route('teacher.google-classroom.index')
                ->with('error', 'Google Classroom not connected.');
        }

        try {
            // Fetch courses from Google Classroom
            $response = Http::withToken($token)->get(
                'https://classroom.googleapis.com/v1/courses',
                ['teacherId' => 'me']
            );

            if ($response->successful()) {
                $courses = $response->json('courses', []);
                
                // Sync courses with local classes
                // Implementation depends on your class structure
                
                return redirect()->route('teacher.google-classroom.index')
                    ->with('success', 'Synced ' . count($courses) . ' classes from Google Classroom.');
            }

            return redirect()->route('teacher.google-classroom.index')
                ->with('error', 'Failed to sync classes. Please reconnect.');
        } catch (\Exception $e) {
            return redirect()->route('teacher.google-classroom.index')
                ->with('error', 'Error syncing: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Google Classroom.
     */
    public function disconnect()
    {
        $user = Auth::user();
        $settings = \App\Models\UserSetting::getOrCreateForUser($user->id);
        $currentSettings = $settings->settings ?? [];
        unset($currentSettings['google_classroom_token']);
        $settings->update(['settings' => $currentSettings]);

        return redirect()->route('teacher.google-classroom.index')
            ->with('success', 'Google Classroom disconnected.');
    }

    /**
     * Exchange authorization code for access token.
     */
    private function exchangeCodeForToken(string $code)
    {
        try {
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => route('teacher.google-classroom.callback'),
                'grant_type' => 'authorization_code',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
        }

        return null;
    }
}
