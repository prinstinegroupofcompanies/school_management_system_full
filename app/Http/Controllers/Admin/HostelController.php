<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelPayment;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hostel::with('rooms');
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhere('warden_name', 'like', "%{$q}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $hostels = $query->latest()->paginate(15)->withQueryString();
        return view('admin.hostel.index', compact('hostels'));
    }

    public function create()
    {
        return view('admin.hostel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'warden_name' => 'nullable|string|max:255',
            'warden_phone' => 'nullable|string|max:20',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        Hostel::create($request->all());

        return redirect()->route('admin.hostel.index')
            ->with('success', 'Hostel created successfully.');
    }

    public function show(Hostel $hostel)
    {
        $hostel->load('rooms');
        return view('admin.hostel.show', compact('hostel'));
    }

    public function edit(Hostel $hostel)
    {
        return view('admin.hostel.edit', compact('hostel'));
    }

    public function update(Request $request, Hostel $hostel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'warden_name' => 'nullable|string|max:255',
            'warden_phone' => 'nullable|string|max:20',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        $hostel->update($request->all());

        return redirect()->route('admin.hostel.index')
            ->with('success', 'Hostel updated successfully.');
    }

    public function destroy(Hostel $hostel)
    {
        $hostel->delete();

        return redirect()->route('admin.hostel.index')
            ->with('success', 'Hostel deleted successfully.');
    }

    // Hostel Rooms Management
    public function rooms()
    {
        $rooms = HostelRoom::with('hostel')->latest()->paginate(15);
        return view('admin.hostel.rooms', compact('rooms'));
    }

    public function createRoom()
    {
        $hostels = Hostel::where('status', 'active')->get();
        return view('admin.hostel.create-room', compact('hostels'));
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|max:50',
            'room_name' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|integer|min:0',
            'wing' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'room_size' => 'nullable|numeric|min:0',
            'furniture' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'air_conditioning' => 'boolean',
            'heating' => 'boolean',
            'internet' => 'boolean',
            'bathroom_type' => 'nullable|string|in:private,shared',
            'kitchen_facility' => 'boolean',
            'laundry_facility' => 'boolean',
            'monthly_rent' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'rent_type' => 'nullable|string|in:monthly,quarterly,semester,annual',
            'security_deposit' => 'required|numeric|min:0',
            'utility_charges' => 'required|numeric|min:0',
            'status' => 'required|string|in:available,occupied,maintenance,reserved,cleaning,inactive',
            'description' => 'nullable|string',
            'rules_regulations' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $roomData = $request->all();
        $roomData['amenities'] = json_encode($request->amenities ?? []);
        $roomData['is_active'] = $request->status !== 'inactive';

        HostelRoom::create($roomData);

        return redirect()->route('admin.hostel.rooms')
            ->with('success', 'Hostel room created successfully.');
    }

    public function editRoom(HostelRoom $room)
    {
        $hostels = Hostel::where('status', 'active')->get();
        return view('admin.hostel.edit-room', compact('room', 'hostels'));
    }

    public function updateRoom(Request $request, HostelRoom $room)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|max:50',
            'room_name' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:100',
            'floor' => 'nullable|integer|min:0',
            'wing' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'room_size' => 'nullable|numeric|min:0',
            'furniture' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'air_conditioning' => 'boolean',
            'heating' => 'boolean',
            'internet' => 'boolean',
            'bathroom_type' => 'nullable|string|in:private,shared',
            'kitchen_facility' => 'boolean',
            'laundry_facility' => 'boolean',
            'monthly_rent' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'rent_type' => 'nullable|string|in:monthly,quarterly,semester,annual',
            'security_deposit' => 'required|numeric|min:0',
            'utility_charges' => 'required|numeric|min:0',
            'status' => 'required|string|in:available,occupied,maintenance,reserved,cleaning,inactive',
            'description' => 'nullable|string',
            'rules_regulations' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $roomData = $request->all();
        $roomData['amenities'] = json_encode($request->amenities ?? []);
        $roomData['is_active'] = $request->status !== 'inactive';

        $room->update($roomData);

        return redirect()->route('admin.hostel.rooms')
            ->with('success', 'Hostel room updated successfully.');
    }

    public function destroyRoom(HostelRoom $room)
    {
        $room->delete();

        return redirect()->route('admin.hostel.rooms')
            ->with('success', 'Hostel room deleted successfully.');
    }

    // Hostel Payments Management
    public function payments()
    {
        $payments = HostelPayment::with(['student', 'hostelRoom.hostel'])
            ->latest()
            ->paginate(15);
        return view('admin.hostel.payments', compact('payments'));
    }

    public function createPayment()
    {
        $students = \App\Models\Student::with('user')->get();
        $rooms = HostelRoom::with('hostel')->where('is_active', true)->get();
        return view('admin.hostel.create-payment', compact('students', 'rooms'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'hostel_room_id' => 'nullable|exists:hostel_rooms,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        HostelPayment::create([
            'student_id' => $request->student_id,
            'hostel_room_id' => $request->hostel_room_id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.hostel.payments')
            ->with('success', 'Hostel payment created successfully.');
    }

    public function updatePaymentStatus(Request $request, HostelPayment $payment)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,overdue,cancelled',
            'payment_date' => 'nullable|date',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $updateData = ['status' => $request->status];
        
        if ($request->status === 'paid' && $request->payment_date) {
            $updateData['paid_date'] = $request->payment_date;
        }
        
        if ($request->transaction_id) {
            $updateData['transaction_id'] = $request->transaction_id;
        }

        $payment->update($updateData);

        return redirect()->route('admin.hostel.payments')
            ->with('success', 'Payment status updated successfully.');
    }
}