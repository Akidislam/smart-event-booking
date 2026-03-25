<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $featuredVenues = Venue::active()
            ->orderByDesc('rating')
            ->take(6)
            ->get();

        $upcomingEvents = Event::approved()
            ->upcoming()
            ->with(['venue', 'user'])
            ->orderBy('start_datetime')
            ->take(6)
            ->get();

        $stats = [
            'venues' => Venue::active()->count(),
            'events' => Event::approved()->count(),
            'bookings' => Booking::where('status', 'confirmed')->count(),
            'users' => \App\Models\User::count(),
        ];

        return view('home', compact('featuredVenues', 'upcomingEvents', 'stats'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        $myBookings = Booking::where('user_id', $user->id)
            ->with(['venue', 'event'])
            ->latest()
            ->take(5)
            ->get();

        $myEvents = Event::where('user_id', $user->id)
            ->with('venue')
            ->latest()
            ->take(5)
            ->get();

        $myVenues = Venue::where('user_id', $user->id)
            ->withCount('bookings')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'bookings' => Booking::where('user_id', $user->id)->count(),
            'events' => Event::where('user_id', $user->id)->count(),
            'venues' => Venue::where('user_id', $user->id)->count(),
            'spent' => Booking::where('user_id', $user->id)->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('dashboard', compact('myBookings', 'myEvents', 'myVenues', 'stats'));
    }
}
