<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('super_admin');
    }

    public function index()
    {
        $schoolsCount = School::count();
        $activeSchools = School::where('is_active', true)->count();
        $recentSchools = School::withCount('users')->latest()->take(5)->get();
        $totalUsers = User::whereNotNull('school_id')->count();

        return view('super_admin.dashboard', compact('schoolsCount', 'activeSchools', 'recentSchools', 'totalUsers'));
    }
}
