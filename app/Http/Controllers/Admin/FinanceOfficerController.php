<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceOfficer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FinanceOfficerController extends Controller
{
    public function index()
    {
        $officers = FinanceOfficer::with('user')->paginate(15);
        return view('admin.finance_officers.index', compact('officers'));
    }

    public function create()
    {
        return view('admin.finance_officers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'finance',
            'status' => 'active',
        ]);

        FinanceOfficer::create([
            'user_id' => $user->id,
            'finance_officer_id' => 'FIN' . str_pad((FinanceOfficer::count() + 1), 4, '0', STR_PAD_LEFT),
            'status' => 'active',
        ]);

        return redirect()->route('admin.finance_officers.index')->with('success', 'Finance officer created successfully');
    }
}


