@extends('layouts.app')

@section('title', 'My Events - EventVenue')

@section('content')
<div class="page-header py-4 mb-4">
    <div class="container d-flex justify-between align-center">
        <div>
            <h1>My Managed <span class="text-gradient">Events</span></h1>
            <p>Track ticket sales, manage attendees, and update event details.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="{{ route('events.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Event</a>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="card p-0 mb-5">
        <div class="table-wrap">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:var(--bg-surface);">
                    <tr>
                        <th style="padding:1rem 1.5rem; width:120px;">Date</th>
                        <th>Event Details</th>
                        <th>Ticketing / Sales</th>
                        <th>Calendar Sync</th>
                        <th style="text-align:right; padding-right:1.5rem;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $e)
                    <tr>
                        <td style="padding:1.5rem;">
                            <div class="fw-bold text-center p-2 rounded" style="background:var(--bg-surface); border:1px solid var(--border);">
                                <div style="color:var(--primary-light); font-size:1.5rem; font-weight:900; line-height:1;">{{ $e->start_datetime->format('d') }}</div>
                                <div style="text-transform:uppercase; font-size:.7rem; font-weight:700; margin-top:.25rem;">{{ $e->start_datetime->format('M Y') }}</div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('events.show', $e) }}" target="_blank" class="fw-bold fs-md text-primary d-block mb-1" style="text-decoration:none; font-size:1.1rem;">{{ $e->title }}</a>
                            <div class="text-muted fs-sm mb-1"><i class="fas fa-clock"></i> {{ $e->start_datetime->format('h:i A') }} - {{ $e->end_datetime->format('h:i A') }}</div>
                            @if($e->venue)
                                <div class="text-muted" style="font-size:.75rem;"><i class="fas fa-location-dot text-danger"></i> {{ $e->venue->name }}, {{ $e->venue->city }}</div>
                            @else
                                <div class="text-muted" style="font-size:.75rem;"><i class="fas fa-globe text-info"></i> Online / Details TBA</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-center gap-3">
                                <div>
                                    <div class="fs-sm text-muted text-uppercase fw-bold mb-1" style="font-size:.65rem;letter-spacing:1px;">Price</div>
                                    <div class="fw-bold">{{ $e->is_free ? 'FREE' : $e->ticket_price_formatted }}</div>
                                </div>
                                <div style="width:1px; height:24px; background:var(--border);"></div>
                                <div>
                                    <div class="fs-sm text-muted text-uppercase fw-bold mb-1" style="font-size:.65rem;letter-spacing:1px;">Capacity</div>
                                    <div class="fw-bold text-success">{{ $e->attendees_count }} <span class="text-muted fw-normal">/ {{ $e->max_attendees ?? '&infin;' }}</span></div>
                                </div>
                            </div>
                            
                            @if($e->max_attendees)
                                <div style="width:100px; height:4px; background:var(--bg-surface); border-radius:2px; margin-top:.5rem; overflow:hidden;">
                                    <div style="height:100%; background:var(--success); width:{{ ($e->attendees_count / $e->max_attendees) * 100 }}%"></div>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($e->google_calendar_event_id && auth()->user()->google_token)
                                <div class="badge badge-primary" style="background:rgba(59,130,246,0.1); color:#60a5fa;"><i class="fab fa-google"></i> Synced to Primary</div>
                            @else
                                <a href="{{ route('auth.google') }}" class="badge badge-secondary" style="text-decoration:none;"><i class="fas fa-link"></i> Connect Output</a>
                            @endif
                        </td>
                        <td style="text-align:right; padding-right:1.5rem; vertical-align:middle;">
                            <div class="d-flex justify-between gap-2" style="justify-content:flex-end;">
                                <a href="{{ route('events.show', $e) }}" class="btn btn-outline btn-sm" title="View Public Page"><i class="fas fa-external-link-alt"></i></a>
                                <a href="{{ route('events.edit', $e) }}" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i> Edit</a>
                                <form action="{{ route('events.destroy', $e) }}" method="POST" onsubmit="return confirm('Delete this event? All associated bookings will be marked cancelled.');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline btn-sm border-danger text-danger" title="Cancel Event"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-calendar-times mb-3" style="font-size:3rem;opacity:.3;display:block;"></i> You haven't organized any events yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if(count($events) > 0)
    <div class="d-flex justify-center">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection
