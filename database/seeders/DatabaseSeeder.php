<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Venue;
use App\Models\Event;
use App\Models\Booking;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@eventvenue.bd',
            'phone' => '+8801700000001',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. Create Venue Host
        $host = User::create([
            'name' => 'Dhaka Real Estate Corp.',
            'email' => 'host@eventvenue.bd',
            'phone' => '+8801700000002',
            'password' => Hash::make('password'),
            'role' => 'venue_owner',
            'email_verified_at' => now(),
        ]);

        // 3. Create Standard User
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // 4. Seed Venues
        $venue1 = Venue::create([
            'user_id' => $host->id,
            'name' => 'Grand Pan Pacific Sonargaon Hall',
            'description' => 'A premier 5-star venue right in the heart of Dhaka. High ceilings, luxurious chandeliers, and top-tier acoustics make this perfect for grand weddings and international tech conferences.',
            'address' => '107 Kazi Nazrul Islam Avenue',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'capacity' => 1200,
            'price_per_hour' => 15000,
            'category' => 'conference',
            'status' => 'active',
            'rating' => 4.9,
            'images' => [
                'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=1200',
                'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=1200'
            ],
            'latitude' => 23.7508,
            'longitude' => 90.3934,
        ]);

        $venue2 = Venue::create([
            'user_id' => $host->id,
            'name' => 'Lakeshore Rooftop Garden',
            'description' => 'Stunning open-air rooftop venue overlooking the Gulshan Lake. Perfect for evening networking events, intimate acoustic concerts, or luxurious birthday parties under the stars.',
            'address' => 'Road 41, Gulshan 2',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'capacity' => 250,
            'price_per_hour' => 5000,
            'category' => 'rooftop',
            'status' => 'active',
            'rating' => 4.8,
            'images' => [
                'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200'
            ],
            'latitude' => 23.7925,
            'longitude' => 90.4078,
        ]);

        $venue3 = Venue::create([
            'user_id' => $admin->id,
            'name' => 'Radha Krishnapur Resort',
            'description' => 'A beautiful out-of-town resort perfect for destination weddings or corporate retreats. Features a massive central lawn, swimming pool, and fully air-conditioned chalets.',
            'address' => 'Gazipur Sadar',
            'city' => 'Gazipur',
            'state' => 'Dhaka Division',
            'capacity' => 5000,
            'price_per_hour' => 12000,
            'category' => 'outdoor',
            'status' => 'active',
            'rating' => 4.5,
            'images' => [
                'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=1200'
            ],
        ]);

        // 5. Seed Events
        $event1 = Event::create([
            'user_id' => $admin->id,
            'venue_id' => $venue1->id,
            'title' => 'Bangladesh Web Summit 2026',
            'description' => 'Join the largest gathering of developers, designers, and tech entrepreneurs in Bangladesh. Featuring keynotes from industry leaders, hands-on workshops, and incredible networking opportunities.',
            'category' => 'conference',
            'start_datetime' => now()->addDays(14)->setTime(9, 0),
            'end_datetime' => now()->addDays(14)->setTime(18, 0),
            'max_attendees' => 800,
            'ticket_price' => 2500,
            'is_free' => false,
            'status' => 'published',
            'banner_image' => null, // fallback will be used
        ]);

        $event2 = Event::create([
            'user_id' => $host->id,
            'venue_id' => $venue2->id,
            'title' => 'Jazz Night Under the Stars',
            'description' => 'Enjoy a smooth evening of live jazz music overlooking the city skyline. Food and drinks will be available for purchase at the venue.',
            'category' => 'concert',
            'start_datetime' => now()->addDays(3)->setTime(19, 30),
            'end_datetime' => now()->addDays(3)->setTime(23, 0),
            'max_attendees' => 150,
            'ticket_price' => 0,
            'is_free' => true,
            'status' => 'published',
        ]);

        // 6. Seed Bookings
        Booking::create([
            'user_id' => $user->id,
            'event_id' => $event1->id,
            'venue_id' => $venue1->id,
            'start_datetime' => $event1->start_datetime,
            'end_datetime' => $event1->end_datetime,
            'attendees' => 2,
            'total_amount' => 5000,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'special_requests' => 'Vegetarian meal preferred.',
            'booking_reference' => 'BK-SEEDS1'
        ]);

        Booking::create([
            'user_id' => $user->id,
            'event_id' => $event2->id,
            'venue_id' => $venue2->id,
            'start_datetime' => $event2->start_datetime,
            'end_datetime' => $event2->end_datetime,
            'attendees' => 4,
            'total_amount' => 0,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_reference' => 'BK-SEEDS2'
        ]);

        Booking::create([
            'user_id' => $user->id,
            'venue_id' => $venue3->id,
            'start_datetime' => now()->addDays(30)->setTime(10, 0),
            'end_datetime' => now()->addDays(30)->setTime(22, 0),
            'attendees' => 400,
            'total_amount' => 12 * 12000, // 12 hours * 12k
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'booking_reference' => 'BK-SEEDS3'
        ]);
    }
}
