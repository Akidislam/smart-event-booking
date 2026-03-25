@extends('layouts.app')

@section('title', $event->title . ' - EventVenue')

@push('styles')
<style>
    .event-hero {
        position: relative; height: 50vh; min-height: 400px;
        background-image: url('{{ $event->banner_image ? asset('storage/'.$event->banner_image) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1600' }}');
        background-size: cover; background-position: center; border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 3rem; margin-top: 1rem;
    }
    .event-hero-overlay {
        position: absolute; inset: 0; background: linear-gradient(to top, var(--bg-card) 10%, rgba(26,26,53,0.5) 50%, rgba(26,26,53,0.2) 100%);
        display: flex; flex-direction: column; justify-content: flex-end; padding: 3rem;
    }
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; margin-bottom: 4rem; }
    
    .date-badge-large { background: rgba(99,102,241,0.15); border: 2px solid var(--primary); border-radius: var(--radius-sm); display: inline-flex; flex-direction: column; align-items: center; justify-content: center; padding: .75rem 1.5rem; text-align: center; }
    .date-badge-large .day { font-size: 2.5rem; font-weight: 900; line-height: 1; color: var(--primary-light); }
    .date-badge-large .month { font-size: .9rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text); margin-bottom: .25rem; }
    
    .event-info-panel { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 2rem; }
    .info-row { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.25rem; }
    .info-row:last-child { margin-bottom: 0; }
    .info-icon { width: 44px; height: 44px; border-radius: 50%; background: var(--bg-card); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: var(--primary-light); flex-shrink: 0; }
    .info-content { flex: 1; }
    .info-title { font-size: .85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; font-weight: 700; margin-bottom: .25rem; }
    .info-detail { font-size: 1.05rem; font-weight: 500; }
    
    .ticket-card { background: linear-gradient(135deg, var(--bg-card), var(--bg-surface)); border: 1px solid var(--primary); border-radius: var(--radius-lg); padding: 2rem; position: sticky; top: calc(var(--nav-h) + 2rem); box-shadow: 0 20px 40px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1); }
    
    @media (max-width: 900px) {
        .content-grid { grid-template-columns: 1fr; }
        .event-hero-overlay { padding: 2rem; }
        .ticket-card { position: static; margin-top: 3rem; }
    }
</style>
@endpush

@section('content')
<div class="container pb-5">

    <div class="event-hero">
        <div class="event-hero-overlay">
            <div class="d-flex align-center gap-2 mb-3">
                <span class="badge badge-primary" style="backdrop-filter:blur(4px)">{{ \App\Models\Event::categories()[$event->category] ?? ucfirst($event->category) }}</span>
                @if($event->is_free)
                    <span class="badge badge-success" style="backdrop-filter:blur(4px)">Free Event</span>
                @endif
            </div>
            <div class="d-flex align-end justify-between" style="width:100%;">
                <div>
                    <h1 style="font-family:'Plus Jakarta Sans',sans-serif; font-size:3rem; font-weight:900; line-height:1.1; margin-bottom:1rem; text-shadow:0 4px 10px rgba(0,0,0,0.5);">{{ $event->title }}</h1>
                    <div class="d-flex align-center gap-3">
                        <div class="d-flex align-center gap-2">
                            <img src="{{ $event->user->avatar_url }}" style="width:36px;height:36px;border-radius:50%;" alt="">
                            <span>By <strong class="fw-bold">{{ $event->user->name }}</strong></span>
                        </div>
                        <span>&bull;</span>
                        <span class="text-muted"><i class="fas fa-calendar-check text-primary"></i> Added {{ $event->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                
                <div class="d-flex gap-2 align-center">
                    @can('update', $event)
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-secondary" style="background:rgba(255,255,255,0.1); backdrop-filter:blur(4px);"><i class="fas fa-pen"></i> Edit</a>
                        <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline" style="border-color:var(--danger); color:#fff; background:var(--danger);"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Main Details -->
        <div>
            <div class="d-flex gap-4 mb-4" style="align-items:center;">
                <div class="date-badge-large">
                    <div class="month">{{ $event->start_datetime->format('F') }}</div>
                    <div class="day">{{ $event->start_datetime->format('d') }}</div>
                </div>
                <div>
                    <h2 class="fw-bold mb-1" style="font-size:1.5rem;">{{ $event->start_datetime->format('l, F j, Y') }}</h2>
                    <p class="text-muted" style="font-size:1.1rem;">{{ $event->start_datetime->format('h:i A') }} - {{ $event->end_datetime->format('h:i A') }} ({{ $event->duration }})</p>
                </div>
            </div>

            <div class="event-info-panel">
                <div class="info-row">
                    <div class="info-icon"><i class="fas fa-location-dot"></i></div>
                    <div class="info-content">
                        <div class="info-title">Location</div>
                        @if($event->venue)
                            <div class="info-detail" style="margin-bottom:.5rem;">{{ $event->venue->name }}</div>
                            <div class="text-muted fs-sm">{{ $event->venue->address }}, {{ $event->venue->city }}</div>
                            <a href="{{ route('venues.show', $event->venue) }}" class="btn btn-outline btn-sm mt-2"><i class="fas fa-external-link-alt"></i> View Venue</a>
                        @else
                            <div class="info-detail">TBA / Online</div>
                        @endif
                    </div>
                </div>
                <div class="info-row" style="padding-top:1.25rem; border-top:1px dashed var(--border);">
                    <div class="info-icon" style="color:var(--secondary)"><i class="fas fa-users"></i></div>
                    <div class="info-content">
                        <div class="info-title">Capacity & Attendance</div>
                        <div class="info-detail">{{ $event->max_attendees ? number_format($event->attendees_count) . ' / ' . number_format($event->max_attendees) . ' Spots Filled' : 'Unlimited Capacity' }}</div>
                        @if($event->max_attendees)
                            <div style="width:100%; height:6px; background:var(--bg); border-radius:3px; margin-top:.75rem; overflow:hidden;">
                                <div style="height:100%; background:var(--primary); width:{{ ($event->attendees_count / $event->max_attendees) * 100 }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <h3 class="fw-bold mb-3 pb-2 border-bottom" style="font-size:1.35rem; border-color:var(--border-strong);">About this Event</h3>
            <div style="line-height:1.8; color:var(--text-muted); font-size:1.05rem;" class="mb-5">
                {!! nl2br(e($event->description ?? 'No description provided.')) !!}
            </div>

        </div>

        <!-- Sticky Form -->
        <div>
            <div class="ticket-card">
                <div style="text-align:center; padding-bottom:1.5rem; border-bottom:1px dashed rgba(255,255,255,0.2); margin-bottom:1.5rem;">
                    <div class="fs-sm text-uppercase fw-bold text-primary mb-1">Ticket Price</div>
                    <div style="font-size:3rem; font-weight:900; line-height:1; font-family:'Plus Jakarta Sans',sans-serif;">{{ $event->ticket_price_formatted }}</div>
                </div>

                @if($event->start_datetime < now())
                    <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-circle"></i> This event has already ended.</div>
                @elseif($event->max_attendees && $event->attendees_count >= $event->max_attendees)
                    <div class="alert alert-error mb-0"><i class="fas fa-times-circle"></i> Sold Out!</div>
                @else
                    <form action="{{ route('bookings.event.store', $event) }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="form-label text-muted">Number of Tickets</label>
                            <div class="input-icon">
                                <i class="fas fa-ticket-alt"></i>
                                <input type="number" id="ticketCount" name="attendees" class="form-control pl-4" min="1" max="{{ $event->max_attendees ? ($event->max_attendees - $event->attendees_count) : 10 }}" value="1" required>
                            </div>
                            @if($event->max_attendees)
                                <div class="fs-sm text-muted mt-2 text-right">Max {{ $event->max_attendees - $event->attendees_count }} available</div>
                            @endif
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label text-muted">Special Requests (Optional)</label>
                            <textarea name="special_requests" class="form-control" rows="2" placeholder="Dietary requirements, accessibility needs etc..."></textarea>
                        </div>

                        @if(!$event->is_free)
                        <div class="d-flex justify-between align-center mb-4 pb-2 border-bottom fw-bold" style="border-color:rgba(255,255,255,0.1)">
                            <span>Total Area</span>
                            <span id="totalPrice" class="text-success fs-lg">{{ $event->ticket_price_formatted }}</span>
                        </div>
                        @endif

                        @auth
                            <button type="submit" class="btn btn-primary btn-lg w-full justify-center shadow-lg"><i class="fas fa-check-circle"></i> Confirm Booking</button>
                            <div class="text-center text-muted fs-sm mt-3"><i class="fab fa-google"></i> Calendar sync enabled</div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-secondary w-full justify-center">Sign In to Book</a>
                        @endauth
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(!$event->is_free)
<script>
    document.getElementById('ticketCount')?.addEventListener('input', function() {
        const count = parseInt(this.value) || 0;
        const price = {{ (float) $event->ticket_price }};
        const total = (count * price).toLocaleString();
        document.getElementById('totalPrice').innerText = '৳' + total;
    });
</script>
@endif
@endpush
@endsection
