<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected GoogleCalendarService $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(Request $request)
    {
        $query = Event::approved()->upcoming()->with(['venue', 'user']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('start_datetime', $request->date);
        }

        if ($request->filled('is_free')) {
            $query->where('is_free', $request->is_free === 'true');
        }

        $events = $query->orderBy('start_datetime')->paginate(9);
        $categories = Event::categories();

        return view('events.index', compact('events', 'categories'));
    }

    public function show(Event $event)
    {
        $event->load(['venue', 'user', 'bookings']);
        return view('events.show', compact('event'));
    }

    public function create()
    {
        $venues = Venue::active()->where('user_id', Auth::id())->pluck('name', 'id');
        $allVenues = Venue::active()->pluck('name', 'id');
        $categories = Event::categories();
        return view('events.create', compact('venues', 'allVenues', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'max_attendees' => 'nullable|integer|min:1',
            'ticket_price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'is_public' => 'boolean',
            'venue_id' => 'nullable|exists:venues,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events', 'public');
        }

        $event = Event::create($validated);

        // Sync to Google Calendar
        try {
            $calendarEventId = $this->calendarService->createEvent($event, Auth::user());
            if ($calendarEventId) {
                $event->update(['google_calendar_event_id' => $calendarEventId]);
            }
        }
        catch (\Exception $e) {
        // Calendar sync failed silently
        }

        return redirect()->route('events.show', $event)->with('success', 'Event created and synced to Google Calendar!');
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $allVenues = Venue::active()->pluck('name', 'id');
        $categories = Event::categories();
        return view('events.edit', compact('event', 'allVenues', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'max_attendees' => 'nullable|integer|min:1',
            'ticket_price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'is_public' => 'boolean',
            'status' => 'in:pending,approved,rejected,cancelled',
            'venue_id' => 'nullable|exists:venues,id',
        ]);

        $event->update($validated);

        // Update Google Calendar event
        try {
            if ($event->google_calendar_event_id) {
                $this->calendarService->updateEvent($event, Auth::user());
            }
        }
        catch (\Exception $e) {
        // Silent fail
        }

        return redirect()->route('events.show', $event)->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        try {
            if ($event->google_calendar_event_id) {
                $this->calendarService->deleteEvent($event, Auth::user());
            }
        }
        catch (\Exception $e) {
        // Silent fail
        }

        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }

    public function myEvents()
    {
        $events = Event::where('user_id', Auth::id())
            ->with('venue')
            ->latest()
            ->paginate(10);

        return view('events.my-events', compact('events'));
    }
}
