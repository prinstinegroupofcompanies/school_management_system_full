<?php

namespace App\Http\Middleware;

use App\Models\StudentFee;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceFeeCompliance
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->user_type === 'student') {
            $student = $user->student ?? null;
            if ($student) {
                $overdue = StudentFee::query()
                    ->where('student_id', $student->id)
                    ->where('balance', '>', 0)
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', now()->toDateString())
                    ->exists();

                if ($overdue) {
                    if (!$request->is('student/dashboard/finance*')) {
                        return redirect()->route('student.finance.index')
                            ->with('error', 'Your fees are overdue. Please clear dues to continue.');
                    }
                }
            }
        }

        return $next($request);
    }
}


