<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Venue::active()->with('user');

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('location')) {
            $query->where(function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->location . '%')
                    ->orWhere('address', 'like', '%' . $request->location . '%');
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_hour', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_hour', '<=', $request->max_price);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        if ($request->filled('date')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->whereDate('start_datetime', '<=', $request->date)
                    ->whereDate('end_datetime', '>=', $request->date);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        // Only return HTML content if it's an AJAX request to avoid loading full page layout
        if ($request->ajax()) {
            $venues = $query->orderByDesc('rating')->paginate(9);
            return view('venues.partials.listing', compact('venues'))->render();
        }

        $venues = $query->orderByDesc('rating')->paginate(9);
        $categories = Venue::categories();

        // All venues with coordinates for map
        $mapVenues = Venue::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'name', 'address', 'latitude', 'longitude', 'price_per_hour', 'category']);

        return view('venues.index', compact('venues', 'categories', 'mapVenues'));
    }

    public function show(Venue $venue)
    {
        $venue->load(['user', 'events' => function ($q) {
            $q->approved()->upcoming()->orderBy('start_datetime');
        }]);

        $relatedVenues = Venue::active()
            ->where('category', $venue->category)
            ->where('id', '!=', $venue->id)
            ->take(3)
            ->get();

        return view('venues.show', compact('venue', 'relatedVenues'));
    }

    public function create()
    {
        $this->authorize('create', Venue::class);
        $categories = Venue::categories();
        return view('venues.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Venue::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'place_id' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'category' => 'required|string',
            'amenities' => 'nullable|array',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['user_id'] = Auth::id();

        // Handle image uploads
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('venues', 'public');
            $validated['image'] = $path;
        }

        $venue = Venue::create($validated);

        // Auto-promote user to venue_owner role on their first listing
        $user = Auth::user();
        if ($user->role === 'user' || $user->role === null) {
            $user->update(['role' => 'venue_owner']);
        }

        return redirect()->route('venues.show', $venue)->with('success', 'Venue created successfully!');
    }

    public function edit(Venue $venue)
    {
        $this->authorize('update', $venue);
        $categories = Venue::categories();
        return view('venues.edit', compact('venue', 'categories'));
    }

    public function update(Request $request, Venue $venue)
    {
        $this->authorize('update', $venue);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'place_id' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'category' => 'required|string',
            'amenities' => 'nullable|array',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle image uploads
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('venues', 'public');
            $validated['image'] = $path;
        }

        $venue->update($validated);

        return redirect()->route('venues.show', $venue)->with('success', 'Venue updated successfully!');
    }

    public function destroy(Venue $venue)
    {
        $this->authorize('delete', $venue);
        $venue->delete();
        return redirect()->route('venues.index')->with('success', 'Venue deleted successfully!');
    }

    public function myVenues()
    {
        $venues = Venue::where('user_id', Auth::id())
            ->withCount('bookings')
            ->latest()
            ->paginate(10);

        return view('venues.my-venues', compact('venues'));
    }

    public function book(Request $request, Venue $venue)
    {
        $validated = $request->validate([
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'attendees' => 'required|integer|min:1|max:' . $venue->capacity,
        ]);

        $hours = ceil(
            (strtotime($validated['end_datetime']) - strtotime($validated['start_datetime'])) / 3600
        );
        $totalAmount = $hours * $venue->price_per_hour;

        $booking = \App\Models\Booking::create([
            'user_id' => Auth::id(),
            'venue_id' => $venue->id,
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'attendees' => $validated['attendees'],
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        try {
            $calendarService = app(\App\Services\GoogleCalendarService::class);
            $calendarEventId = $calendarService->createBookingEvent($booking, Auth::user());
            if ($calendarEventId) {
                $booking->update(['google_calendar_event_id' => $calendarEventId]);
            }
        }
        catch (\Exception $e) {
        // Silent fail
        }

        return redirect()->route('venues.show', $venue)
            ->with('success', 'Venue booked successfully! Reference: ' . $booking->booking_reference);
    }
}
