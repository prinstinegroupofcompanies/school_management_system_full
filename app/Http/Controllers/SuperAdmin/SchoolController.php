<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolAddon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware('super_admin');
    }

    public function index(Request $request)
    {
        $query = School::withCount('users')->with('addons');
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        $schools = $query->latest()->paginate(15)->withQueryString();
        return view('super_admin.schools.index', compact('schools'));
    }

    public function create()
    {
        $features = config('school_features', []);
        return view('super_admin.schools.create', compact('features'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:schools,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'login_background_image' => 'nullable|string|max:500',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => ['required', 'confirmed', Password::defaults()],
            'addons' => 'nullable|array',
            'addons.*' => 'string|in:' . implode(',', array_keys(config('school_features', []))),
        ]);

        $school = School::create([
            'name' => $request->name,
            'code' => $request->code,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'website' => $request->website,
            'login_background_image' => $request->login_background_image,
            'is_active' => true,
        ]);

        foreach ($request->input('addons', []) as $featureKey) {
            SchoolAddon::create([
                'school_id' => $school->id,
                'feature_key' => $featureKey,
                'enabled' => true,
            ]);
        }

        $admin = User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'username' => $request->admin_email,
            'password' => $request->admin_password, // hashed by User model's 'hashed' cast
            'user_type' => 'admin',
            'school_id' => $school->id,
            'is_active' => true,
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        return redirect()->route('super_admin.schools.show', $school)
            ->with('success', 'School created. Admin can log in with ' . $request->admin_email);
    }

    public function show(School $school)
    {
        $school->load(['addons', 'users']);
        $features = config('school_features', []);
        return view('super_admin.schools.show', compact('school', 'features'));
    }

    public function edit(School $school)
    {
        $school->load('addons');
        $features = config('school_features', []);
        return view('super_admin.schools.edit', compact('school', 'features'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:schools,code,' . $school->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'login_background_image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'addons' => 'nullable|array',
            'addons.*' => 'string|in:' . implode(',', array_keys(config('school_features', []))),
        ]);

        $school->update([
            'name' => $request->name,
            'code' => $request->code,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'website' => $request->website,
            'login_background_image' => $request->login_background_image,
            'is_active' => $request->boolean('is_active'),
        ]);

        $requestAddons = $request->input('addons', []);
        foreach (array_keys(config('school_features', [])) as $key) {
            $enabled = in_array($key, $requestAddons);
            SchoolAddon::updateOrCreate(
                ['school_id' => $school->id, 'feature_key' => $key],
                ['enabled' => $enabled]
            );
        }

        return redirect()->route('super_admin.schools.show', $school)
            ->with('success', 'School updated.');
    }

    public function destroy(School $school)
    {
        if ($school->users()->count() > 0) {
            return redirect()->route('super_admin.schools.index')
                ->with('error', 'Cannot delete school with users. Remove or reassign users first.');
        }
        $school->delete();
        return redirect()->route('super_admin.schools.index')->with('success', 'School deleted.');
    }
}
