<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\StudentFee;

class RestrictUnpaid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }

        // Check if user is student or parent
        if ($user->hasRole('student') || $user->hasRole('parent')) {
            $student = $user->student ?? ($user->parent ? $user->parent->students()->first() : null);
            
            if ($student) {
                // Check for unpaid fees that are past due date
                $unpaidFees = \App\Models\StudentFee::where('student_id', $student->id)
                    ->where('status', '!=', 'paid')
                    ->where('balance', '>', 0)
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now())
                    ->exists();
                
                if ($unpaidFees) {
                    // Allow access to finance pages
                    $allowedRoutes = ['student.finance', 'student.finance.index', 'student.fees', 'payment.create'];
                    
                    $routeName = $request->route() ? $request->route()->getName() : null;
                    
                    if ($routeName && !in_array($routeName, $allowedRoutes)) {
                        return redirect()->route('student.finance.index')
                            ->with('error', 'Please pay your outstanding fees to access the system.');
                    }
                }
            }
        }
        
        return $next($request);
    }
}
