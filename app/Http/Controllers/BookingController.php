<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Venue;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected GoogleCalendarService $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['venue', 'event'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['venue', 'event', 'user']);
        return view('bookings.show', compact('booking'));
    }

    public function createVenueBooking(Request $request, Venue $venue)
    {
        return view('bookings.create-venue', compact('venue'));
    }

    public function storeVenueBooking(Request $request, Venue $venue)
    {
        $validated = $request->validate([
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'attendees' => 'required|integer|min:1|max:' . $venue->capacity,
            'special_requests' => 'nullable|string|max:1000',
        ]);

        // Calculate total
        $hours = ceil(
            (strtotime($validated['end_datetime']) - strtotime($validated['start_datetime'])) / 3600
        );
        $totalAmount = $hours * $venue->price_per_hour;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'venue_id' => $venue->id,
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'attendees' => $validated['attendees'],
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'special_requests' => $validated['special_requests'] ?? null,
        ]);

        // Sync to Google Calendar
        try {
            $calendarEventId = $this->calendarService->createBookingEvent($booking, Auth::user());
            if ($calendarEventId) {
                $booking->update(['google_calendar_event_id' => $calendarEventId]);
            }
        }
        catch (\Exception $e) {
        // Silent fail
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Venue booked successfully! Reference: ' . $booking->booking_reference);
    }

    public function createEventBooking(Request $request, Event $event)
    {
        return view('bookings.create-event', compact('event'));
    }

    public function storeEventBooking(Request $request, Event $event)
    {
        $validated = $request->validate([
            'attendees' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $totalAmount = $event->is_free ? 0 : ($event->ticket_price * $validated['attendees']);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'venue_id' => $event->venue_id,
            'start_datetime' => $event->start_datetime,
            'end_datetime' => $event->end_datetime,
            'attendees' => $validated['attendees'],
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
            'payment_status' => $event->is_free ? 'paid' : 'unpaid',
            'special_requests' => $validated['special_requests'] ?? null,
        ]);

        // Sync to Google Calendar
        try {
            $calendarEventId = $this->calendarService->createBookingEvent($booking, Auth::user());
            if ($calendarEventId) {
                $booking->update(['google_calendar_event_id' => $calendarEventId]);
            }
        }
        catch (\Exception $e) {
        // Silent fail
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Event booking confirmed! Reference: ' . $booking->booking_reference);
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('update', $booking);

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        // Remove from Google Calendar
        try {
            if ($booking->google_calendar_event_id) {
                $this->calendarService->deleteBookingEvent($booking, Auth::user());
            }
        }
        catch (\Exception $e) {
        // Silent fail
        }

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
