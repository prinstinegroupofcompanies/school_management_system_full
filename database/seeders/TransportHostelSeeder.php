<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transport;
use App\Models\TransportRoute;
use App\Models\Hostel;
use App\Models\HostelRoom;

class TransportHostelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample transports
        $transport1 = Transport::create([
            'name' => 'School Bus #1',
            'type' => 'bus',
            'capacity' => 50,
            'driver_name' => 'John Smith',
            'driver_phone' => '+1234567890',
            'vehicle_number' => 'SB-001',
            'status' => 'active',
            'description' => 'Main school bus for daily transportation'
        ]);

        $transport2 = Transport::create([
            'name' => 'School Bus #2',
            'type' => 'bus',
            'capacity' => 45,
            'driver_name' => 'Jane Doe',
            'driver_phone' => '+1234567891',
            'vehicle_number' => 'SB-002',
            'status' => 'active',
            'description' => 'Secondary school bus for overflow routes'
        ]);

        // Create sample transport routes
        TransportRoute::create([
            'route_name' => 'Downtown Route',
            'route_code' => 'DT001',
            'description' => 'Main downtown pickup route',
            'route_details' => json_encode(['Central Park', 'Main Street', 'City Center']),
            'start_location' => 'Central Park',
            'end_location' => 'School Main Gate',
            'distance_km' => 15.5,
            'estimated_duration_minutes' => 30,
            'morning_pickup_time' => '07:30:00',
            'morning_dropoff_time' => '08:00:00',
            'afternoon_pickup_time' => '15:30:00',
            'afternoon_dropoff_time' => '16:00:00',
            'fare_amount' => 25.00,
            'currency' => 'USD',
            'fare_type' => 'monthly',
            'max_capacity' => 50,
            'current_capacity' => 0,
            'status' => 'active',
            'is_active' => true,
            'notes' => 'Main downtown pickup route'
        ]);

        TransportRoute::create([
            'route_name' => 'Suburbs Route',
            'route_code' => 'SB001',
            'description' => 'Suburban areas pickup route',
            'route_details' => json_encode(['Green Valley', 'Oak Hills', 'Riverside']),
            'start_location' => 'Green Valley',
            'end_location' => 'School Main Gate',
            'distance_km' => 22.0,
            'estimated_duration_minutes' => 45,
            'morning_pickup_time' => '07:45:00',
            'morning_dropoff_time' => '08:30:00',
            'afternoon_pickup_time' => '15:45:00',
            'afternoon_dropoff_time' => '16:30:00',
            'fare_amount' => 30.00,
            'currency' => 'USD',
            'fare_type' => 'monthly',
            'max_capacity' => 45,
            'current_capacity' => 0,
            'status' => 'active',
            'is_active' => true,
            'notes' => 'Suburban areas pickup route'
        ]);

        // Create sample hostels
        $hostel1 = Hostel::create([
            'name' => 'Liberty Hall',
            'address' => '123 Campus Drive, Monrovia',
            'phone' => '+1234567890',
            'email' => 'liberty@school.edu',
            'warden_name' => 'Mary Johnson',
            'warden_phone' => '+1234567891',
            'capacity' => 100,
            'description' => 'Modern hostel with excellent facilities',
            'status' => 'active'
        ]);

        $hostel2 = Hostel::create([
            'name' => 'Freedom House',
            'address' => '456 University Avenue, Monrovia',
            'phone' => '+1234567892',
            'email' => 'freedom@school.edu',
            'warden_name' => 'David Brown',
            'warden_phone' => '+1234567893',
            'capacity' => 80,
            'description' => 'Cozy hostel with traditional architecture',
            'status' => 'active'
        ]);

        // Create sample hostel rooms
        for ($i = 1; $i <= 20; $i++) {
            HostelRoom::create([
                'hostel_id' => $hostel1->id,
                'room_number' => 'L' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'room_name' => 'Room ' . $i,
                'building' => 'Liberty Hall',
                'floor' => ceil($i / 10),
                'wing' => $i <= 10 ? 'North' : 'South',
                'capacity' => 2,
                'current_occupancy' => rand(0, 2),
                'room_size' => 25.5,
                'furniture' => 'Bed, Desk, Chair, Wardrobe',
                'amenities' => json_encode(['WiFi', 'Air Conditioning', 'Private Bathroom', 'Study Desk']),
                'air_conditioning' => true,
                'heating' => false,
                'internet' => true,
                'bathroom_type' => 'private',
                'kitchen_facility' => false,
                'laundry_facility' => true,
                'monthly_rent' => 150.00,
                'currency' => 'USD',
                'rent_type' => 'monthly',
                'security_deposit' => 100.00,
                'utility_charges' => 25.00,
                'status' => rand(0, 1) ? 'available' : 'occupied',
                'is_active' => true,
                'description' => 'Comfortable double occupancy room'
            ]);
        }

        for ($i = 1; $i <= 16; $i++) {
            HostelRoom::create([
                'hostel_id' => $hostel2->id,
                'room_number' => 'F' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'room_name' => 'Room ' . $i,
                'building' => 'Freedom House',
                'floor' => ceil($i / 8),
                'wing' => $i <= 8 ? 'East' : 'West',
                'capacity' => 1,
                'current_occupancy' => rand(0, 1),
                'room_size' => 20.0,
                'furniture' => 'Single Bed, Desk, Chair, Wardrobe',
                'amenities' => json_encode(['WiFi', 'Shared Bathroom', 'Study Desk', 'Common Room']),
                'air_conditioning' => false,
                'heating' => true,
                'internet' => true,
                'bathroom_type' => 'shared',
                'kitchen_facility' => true,
                'laundry_facility' => true,
                'monthly_rent' => 120.00,
                'currency' => 'USD',
                'rent_type' => 'monthly',
                'security_deposit' => 80.00,
                'utility_charges' => 20.00,
                'status' => rand(0, 1) ? 'available' : 'occupied',
                'is_active' => true,
                'description' => 'Single occupancy room with shared facilities'
            ]);
        }
    }
}