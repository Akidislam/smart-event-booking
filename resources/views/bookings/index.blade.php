@extends('layouts.app')

@section('title', 'My Bookings - EventVenue')

@section('content')
<div class="page-header py-4 mb-4">
    <div class="container mx-auto px-4 d-flex justify-between align-center">
        <div>
            <h1>My Bookings</h1>
            <p>Manage your upcoming and past event and venue reservations.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<div class="container mx-auto px-4 mb-5">
    <div class="card p-0 mb-5">
        <div class="table-wrap">
            <table>
                <thead style="background:var(--bg-surface);">
                    <tr>
                        <th style="padding:1rem 1.5rem;">Booking Reference</th>
                        <th>Details</th>
                        <th>Date & Time (Duration)</th>
                        <th>Attendees</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th style="text-align:right; padding-right:1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td style="padding:1.5rem;">
                            <div class="badge badge-primary" style="font-family:monospace;letter-spacing:1px;font-size:.85rem;padding:.4rem 1rem;">
                                {{ $booking->booking_reference }}
                            </div>
                            <div class="text-muted fs-sm mt-2"><i class="fas fa-calendar-plus text-primary"></i> Added {{ $booking->created_at->format('M d, Y') }}</div>
                        </td>
                        <td>
                            @if($booking->event)
                                <div class="d-flex align-center gap-2 mb-1">
                                    <div class="badge badge-success" style="padding:.2rem .5rem;font-size:.65rem;">EVENT</div>
                                </div>
                                <a href="{{ route('events.show', $booking->event) }}" class="fw-bold fs-sm text-primary" style="text-decoration:none;">{{ $booking->event->title }}</a>
                                @if($booking->venue)
                                    <div class="text-muted fs-sm mt-1"><i class="fas fa-location-dot"></i> {{ $booking->venue->name }}</div>
                                @endif
                            @elseif($booking->venue)
                                <div class="d-flex align-center gap-2 mb-1">
                                    <div class="badge badge-info" style="padding:.2rem .5rem;font-size:.65rem;">VENUE</div>
                                </div>
                                <a href="{{ route('venues.show', $booking->venue) }}" class="fw-bold fs-sm text-primary" style="text-decoration:none;">{{ $booking->venue->name }}</a>
                                <div class="text-muted fs-sm mt-1"><i class="fas fa-location-dot"></i> {{ $booking->venue->city }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold mb-1">{{ $booking->start_datetime->format('M d, Y') }}</div>
                            <div class="text-muted fs-sm"><i class="far fa-clock"></i> {{ $booking->start_datetime->format('h:i A') }} - {{ $booking->end_datetime->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-center gap-1">
                                <i class="fas fa-users text-muted"></i> <span class="fw-bold">{{ $booking->attendees }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold fs-lg mb-1">{{ $booking->total_amount_formatted }}</div>
                            @if($booking->payment_status === 'paid')
                                <span class="badge badge-success" style="font-size:.65rem;padding:.2rem .4rem;">PAID</span>
                            @else
                                <span class="badge badge-warning" style="font-size:.65rem;padding:.2rem .4rem;">UNPAID</span>
                            @endif
                        </td>
                        <td>
                            {!! $booking->status_badge !!}
                            @if($booking->google_calendar_event_id && !in_array($booking->status, ['cancelled']))
                                <div title="Synced to Google Calendar" class="mt-2 text-success" style="font-size:.8rem;"><i class="fab fa-google"></i> Synced</div>
                            @endif
                        </td>
                        <td style="text-align:right; padding-right:1.5rem; vertical-align:middle;">
                            <div class="d-flex justify-between" style="justify-content:flex-end; gap:.5rem;">
                                @if(!in_array($booking->status, ['cancelled', 'completed']) && $booking->start_datetime > now())
                                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm w-full sm:w-auto" style="color:var(--danger); border-color:var(--danger);"><i class="fas fa-times"></i> Cancel</button>
                                    </form>
                                @endif
                                <button class="btn btn-secondary w-full sm:w-auto" title="Download Ticket PDF (Coming Soon)"><i class="fas fa-download"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-ticket-alt mb-3 text-muted" style="font-size:3rem; opacity:.4;"></i>
                            <h3 class="mb-2">No bookings found</h3>
                            <p class="text-muted mb-4">You haven't made any reservations yet.</p>
                            <div class="d-flex gap-2 justify-center">
                                <a href="{{ route('venues.index') }}" class="btn btn-primary w-full sm:w-auto"><i class="fas fa-building"></i> Browse Venues</a>
                                <a href="{{ route('events.index') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-calendar"></i> Find Events</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-center mb-5">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
