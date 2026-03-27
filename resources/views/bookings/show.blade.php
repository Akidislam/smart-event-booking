@extends('layouts.app')

@section('title', 'Booking #' . $booking->booking_reference . ' - EventVenue')

@section('content')
<div class="page-header py-4 mb-4" style="background:var(--bg-card); border-bottom:1px dashed var(--border-strong);">
    <div class="container mx-auto px-4 d-flex justify-between align-center">
        <div>
            <h1 style="color:var(--text); margin-bottom:.25rem;"><i class="fas fa-ticket-alt text-primary"></i> Booking Confirmation</h1>
            <p class="text-muted m-0">Reference: <strong class="text-white">{{ $booking->booking_reference }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-list"></i> All Bookings</a>
            <button class="btn btn-primary w-full sm:w-auto" onclick="window.print()"><i class="fas fa-print"></i> Print Invoice</button>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 mb-5" style="max-width:860px;">
    
    @if($booking->status === 'confirmed')
        <div class="alert alert-success mb-5" style="align-items:center; padding:1.5rem;">
            <i class="fas fa-check-circle" style="font-size:2rem;"></i>
            <div style="flex:1;">
                <strong>Booking Confirmed!</strong><br>
                Your reservation is confirmed. A copy of this receipt has been emailed to you.
            </div>
            @if($booking->google_calendar_event_id && auth()->user()->google_token)
                <div class="badge badge-primary py-2 px-3"><i class="fab fa-google"></i> Saved to Calendar</div>
            @endif
        </div>
    @elseif($booking->status === 'cancelled')
        <div class="alert alert-error mb-5 py-3"><i class="fas fa-ban fa-2x"></i> <div><strong>Booking Cancelled</strong><br>This reservation has been cancelled.</div></div>
    @endif

    <div class="card p-0 overflow-hidden" style="border:1px solid var(--border-strong); box-shadow:0 20px 50px rgba(0,0,0,0.5);">
        
        <!-- Header -->
        <div style="background:var(--bg-card); padding:2.5rem; border-bottom:1px dashed var(--border-strong); position:relative;">
            <div style="position:absolute; top:2.5rem; right:2.5rem; text-align:right;">
                <div class="m-0" style="font-size:2rem; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif;">{!! $booking->status_badge !!}</div>
                <div class="text-muted fs-sm mt-2">Issued: {{ $booking->created_at->format('M d, Y h:ia') }}</div>
            </div>
            
            <h2 class="fw-bold mb-1" style="font-size:1.5rem;">{{ $booking->event ? $booking->event->title : $booking->venue->name }}</h2>
            <div class="text-muted">
                @if($booking->event && $booking->venue)
                    <i class="fas fa-location-dot"></i> {{ $booking->venue->name }}, {{ $booking->venue->city }}
                @elseif($booking->venue)
                    <i class="fas fa-location-dot text-primary"></i> {{ $booking->venue->address }}, {{ $booking->venue->city }}
                @else
                    <i class="fas fa-globe text-info"></i> Virtual / TBA Location
                @endif
            </div>
        </div>

        <!-- Body -->
        <div style="padding:2.5rem; background:var(--bg-surface);">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 gap-4 mb-5">
                <div>
                    <h5 class="text-muted text-uppercase mb-2 fs-sm fw-bold border-bottom pb-2">Schedule Details</h5>
                    <div class="mb-3">
                        <div class="text-muted fs-sm">From Date</div>
                        <div class="fw-bold" style="font-size:1.1rem;">{{ $booking->start_datetime->format('F d, Y - h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-muted fs-sm">To Date</div>
                        <div class="fw-bold" style="font-size:1.1rem;">{{ $booking->end_datetime->format('F d, Y - h:i A') }}</div>
                    </div>
                </div>
                <div>
                    <h5 class="text-muted text-uppercase mb-2 fs-sm fw-bold border-bottom pb-2">Customer Details</h5>
                    <div class="mb-3">
                        <div class="text-muted fs-sm">Booked By</div>
                        <div class="fw-bold" style="font-size:1.1rem;">{{ $booking->user->name }}</div>
                        <div class="text-muted fs-sm">{{ $booking->user->email }}</div>
                    </div>
                    <div>
                        <div class="text-muted fs-sm">Party Size</div>
                        <div class="fw-bold text-primary"><i class="fas fa-users"></i> {{ $booking->attendees }} Guests</div>
                    </div>
                </div>
            </div>
            
            @if($booking->special_requests)
            <div class="mb-5 p-3" style="background:var(--bg-card); border-left:3px solid var(--primary-light); border-radius:var(--radius-sm);">
                <div class="fw-bold mb-1 fs-sm text-uppercase">Special Requests</div>
                <div class="text-muted">{{ $booking->special_requests }}</div>
            </div>
            @endif

            <table style="width:100%; border-collapse:collapse; background:var(--bg-card); border-radius:var(--radius); overflow:hidden;">
                <thead style="background:rgba(255,255,255,0.02); border-bottom:1px solid var(--border);">
                    <tr>
                        <th style="padding:1rem 1.5rem; text-align:left;" class="text-muted text-uppercase fs-sm fw-bold">Description</th>
                        <th style="padding:1rem 1.5rem; text-align:center;" class="text-muted text-uppercase fs-sm fw-bold">Qty</th>
                        <th style="padding:1rem 1.5rem; text-align:right;" class="text-muted text-uppercase fs-sm fw-bold">Unit Price</th>
                        <th style="padding:1rem 1.5rem; text-align:right;" class="text-muted text-uppercase fs-sm fw-bold">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:1.5rem; border-bottom:1px dashed var(--border);">
                            <div class="fw-bold">{{ $booking->event ? 'Event Ticket' : 'Venue Space Rental' }}</div>
                            <div class="text-muted fs-sm">{{ $booking->event ? $booking->event->category : $booking->venue->category }}</div>
                        </td>
                        <td style="padding:1.5rem; text-align:center; border-bottom:1px dashed var(--border);">
                            @if($booking->event)
                                {{ $booking->attendees }}x Tickets
                            @else
                                @php $hrs = max(1, ceil($booking->end_datetime->diffInMinutes($booking->start_datetime)/60)); @endphp
                                {{ $hrs }} Hours
                            @endif
                        </td>
                        <td style="padding:1.5rem; text-align:right; border-bottom:1px dashed var(--border);">
                            {{ $booking->event ? $booking->event->ticket_price_formatted : $booking->venue->price_formatted }}
                        </td>
                        <td style="padding:1.5rem; text-align:right; font-weight:700; border-bottom:1px dashed var(--border);">
                            {{ $booking->total_amount_formatted }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding:1rem 1.5rem; text-align:right;" class="text-muted fw-bold">Total Processed Due:</td>
                        <td style="padding:1rem 1.5rem; text-align:right; font-size:1.5rem; font-weight:900; color:var(--primary-light);">{{ $booking->total_amount_formatted }}</td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="mt-4 pt-4 border-top text-center text-muted fs-sm">
                If you have any questions regarding this booking, contact the host or platform support.<br>
                Thank you for choosing EventVenue.
            </div>
        </div>
    </div>
</div>
@endsection
