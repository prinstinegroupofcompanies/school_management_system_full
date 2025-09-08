<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview.
     */
    public function index(): JsonResponse
    {
        try {
            $data = [
                'statistics' => $this->getStatistics(),
                'recent_activities' => $this->getRecentActivities(),
                'upcoming_events' => $this->getUpcomingEvents(),
            ];

            return $this->successResponse($data, 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->getStatistics();
            return $this->successResponse($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get recent activities.
     */
    public function recentActivities(): JsonResponse
    {
        try {
            $activities = $this->getRecentActivities();
            return $this->successResponse($activities, 'Recent activities retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve recent activities: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics.
     */
    private function getStatistics(): array
    {
        return [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_staff' => Staff::count(),
            'total_classes' => ClassRoom::count(),
            'total_subjects' => Subject::count(),
            'academic_year' => '2024-2025',
            'currency' => 'LRD',
            'currency_symbol' => 'L$',
        ];
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities(): array
    {
        return [
            [
                'id' => 1,
                'type' => 'student_registration',
                'description' => 'New student registered',
                'created_at' => Carbon::now()->subHours(2)->toISOString(),
            ],
            [
                'id' => 2,
                'type' => 'fee_payment',
                'description' => 'Fee payment received',
                'created_at' => Carbon::now()->subHours(4)->toISOString(),
            ],
        ];
    }

    /**
     * Get upcoming events.
     */
    private function getUpcomingEvents(): array
    {
        return [
            'upcoming_exams' => [],
            'holidays' => [],
            'events' => [],
        ];
    }
}
