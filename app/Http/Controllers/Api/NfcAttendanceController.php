<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcCard;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NfcAttendanceController extends Controller
{
    /**
     * Handle NFC card scan for attendance
     * POST /api/nfc/scan
     */
    public function scan(Request $request)
    {
        $request->validate([
            'card_uid' => 'required|string',
            'date' => 'nullable|date',
            'class_id' => 'nullable|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        try {
            $card = NfcCard::findByUid($request->card_uid);

            if (!$card) {
                return response()->json([
                    'success' => false,
                    'message' => 'NFC card not found or inactive'
                ], 404);
            }

            $user = $card->user;
            $date = $request->date ?? now()->toDateString();
            $classId = $request->class_id;
            $subjectId = $request->subject_id;

            // Check if attendance already marked for this date
            $existingAttendance = Attendance::where('attendable_id', $user->id)
                ->where('attendable_type', User::class)
                ->where('date', $date)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance already marked for this date',
                    'attendance' => $existingAttendance
                ], 409);
            }

            // Determine if user is student or teacher
            $attendableType = User::class;
            $attendableId = $user->id;

            if ($user->student) {
                $attendableType = Student::class;
                $attendableId = $user->student->id;
                
                // Use student's class if not provided
                if (!$classId && $user->student->class_id) {
                    $classId = $user->student->class_id;
                }
            }

            // Mark attendance
            $attendance = Attendance::create([
                'attendable_id' => $attendableId,
                'attendable_type' => $attendableType,
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'date' => $date,
                'status' => 'present',
                'recorded_by' => $user->id,
            ]);

            // Update card last used timestamp
            $card->markAsUsed();

            // Broadcast attendance event (for real-time updates)
            event(new \App\Events\AttendanceMarked($attendance));

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully',
                'attendance' => $attendance->load(['attendable', 'class', 'subject']),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'type' => $user->user_type,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('NFC Attendance Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register a new NFC card
     * POST /api/nfc/register
     */
    public function register(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'card_uid' => 'required|string|unique:nfc_cards,card_uid',
        ]);

        try {
            $card = NfcCard::create([
                'user_id' => $request->user_id,
                'card_uid' => $request->card_uid,
                'is_active' => true,
                'registered_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'NFC card registered successfully',
                'card' => $card->load('user')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register card: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance history for a card
     * GET /api/nfc/{card_uid}/attendance
     */
    public function attendanceHistory(Request $request, $cardUid)
    {
        $card = NfcCard::findByUid($cardUid);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'NFC card not found'
            ], 404);
        }

        $user = $card->user;
        $attendableType = $user->student ? Student::class : User::class;
        $attendableId = $user->student ? $user->student->id : $user->id;

        $attendances = Attendance::where('attendable_id', $attendableId)
            ->where('attendable_type', $attendableType)
            ->with(['class', 'subject', 'recordedBy'])
            ->orderBy('date', 'desc')
            ->limit($request->get('limit', 30))
            ->get();

        return response()->json([
            'success' => true,
            'card' => $card,
            'user' => $user,
            'attendances' => $attendances
        ], 200);
    }
}
