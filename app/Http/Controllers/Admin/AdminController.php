<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_venues' => Venue::count(),
            'total_events' => Event::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'pending_venues' => Venue::where('status', 'pending')->count(),
        ];

        $recentBookings = Booking::with(['user', 'venue', 'event'])
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::latest()->take(10)->get();
        $recentVenues = Venue::with('user')->latest()->take(10)->get();

        // Monthly revenue for chart
        $monthlyRevenue = Booking::where('payment_status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->whereYear('created_at', now()->year)
            ->pluck('revenue', 'month')
            ->toArray();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentUsers', 'recentVenues', 'monthlyRevenue'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:user,venue_owner,admin']);
        $user->update(['role' => $request->role]);
        return back()->with('success', 'User role updated successfully.');
    }

    public function venues(Request $request)
    {
        $query = Venue::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $venues = $query->latest()->paginate(20);
        return view('admin.venues', compact('venues'));
    }

    public function approveVenue(Venue $venue)
    {
        $venue->update(['status' => 'active']);
        return back()->with('success', 'Venue approved successfully.');
    }

    public function rejectVenue(Venue $venue)
    {
        $venue->update(['status' => 'inactive']);
        return back()->with('success', 'Venue rejected.');
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'venue', 'event']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(20);
        return view('admin.bookings', compact('bookings'));
    }

    public function events(Request $request)
    {
        $query = Event::with(['user', 'venue']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->latest()->paginate(20);
        return view('admin.events', compact('events'));
    }
}
