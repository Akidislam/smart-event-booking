@extends('layouts.app')

@section('title', 'My Profile — EventVenue')
@section('meta_description', 'View and manage your EventVenue profile, events, and booking history.')

@push('styles')
<style>
/* ─── Layout ─── */
.profile-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 2rem;
    align-items: start;
}

/* ─── Profile Card ─── */
.profile-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    position: sticky;
    top: calc(var(--nav-h) + 1.5rem);
}
.profile-card-banner {
    height: 100px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    position: relative;
}
.profile-card-banner::after {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Ccircle cx='30' cy='30' r='28'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.profile-avatar-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 1.5rem 1.5rem;
    margin-top: -48px;
    position: relative;
    z-index: 1;
}
.profile-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 4px solid var(--bg-card);
    object-fit: cover;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}
.profile-name {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.2rem;
    font-weight: 800;
    margin-top: .75rem;
    text-align: center;
}
.profile-email {
    font-size: .82rem;
    color: var(--text-muted);
    margin-top: .2rem;
    text-align: center;
}
.profile-role {
    margin-top: .6rem;
}
.profile-meta-list {
    list-style: none;
    padding: 0 1.5rem;
    border-top: 1px solid var(--border);
    padding-top: 1.25rem;
    margin-top: 1rem;
}
.profile-meta-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .55rem 0;
    font-size: .875rem;
    color: var(--text-muted);
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.profile-meta-item:last-child { border-bottom: none; }
.profile-meta-item i { width: 18px; color: var(--primary-light); flex-shrink: 0; }
.profile-meta-item span { word-break: break-all; }
.profile-actions {
    padding: 1.25rem 1.5rem;
}

/* ─── Stats Row ─── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}
.stat-pill {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem 1rem;
    text-align: center;
    transition: var(--transition);
}
.stat-pill:hover { border-color: var(--border-strong); transform: translateY(-3px); }
.stat-pill-val {
    font-size: 2rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary-light), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1;
    margin-bottom: .3rem;
}
.stat-pill-label { font-size: .78rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: .05em; }

/* ─── Section Cards ─── */
.section-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.section-card-header {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255,255,255,0.02);
}
.section-card-title {
    font-weight: 700;
    font-size: .95rem;
    display: flex;
    align-items: center;
    gap: .6rem;
}
.section-card-title i { color: var(--primary-light); }
.section-card-body { padding: 1.25rem 1.5rem; }

/* ─── Event row ─── */
.event-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .75rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: var(--transition);
}
.event-row:last-child { border-bottom: none; }
.event-date-box {
    min-width: 50px;
    height: 54px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-sm);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.event-date-box .day { font-size: 1.3rem; font-weight: 900; color: #fff; line-height: 1; }
.event-date-box .mon { font-size: .6rem; font-weight: 700; color: rgba(255,255,255,.7); text-transform: uppercase; }
.event-info { flex: 1; min-width: 0; }
.event-info-title {
    font-weight: 600;
    font-size: .9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.event-info-meta { font-size: .78rem; color: var(--text-muted); margin-top: .2rem; }

/* ─── Booking row ─── */
.booking-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .75rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.booking-row:last-child { border-bottom: none; }
.booking-ref {
    font-family: monospace;
    font-size: .78rem;
    background: rgba(99,102,241,0.1);
    color: var(--primary-light);
    padding: .2rem .6rem;
    border-radius: 4px;
    flex-shrink: 0;
}
.booking-info { flex: 1; min-width: 0; }
.booking-info-title {
    font-weight: 600;
    font-size: .875rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.booking-info-sub { font-size: .77rem; color: var(--text-muted); margin-top: .15rem; }
.booking-amount {
    font-weight: 700;
    font-size: .875rem;
    color: var(--accent);
    flex-shrink: 0;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--text-muted);
}
.empty-state i { font-size: 2.5rem; opacity: .25; display: block; margin-bottom: .75rem; }
.empty-state p { font-size: .9rem; }

/* Responsive */
@media (max-width: 1024px) {
    .profile-grid { grid-template-columns: 1fr; }
    .profile-card { position: static; }
    .stats-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')

<div class="page-header pb-4 mb-0" style="padding-top:2.5rem;">
    <div class="container">
        <div class="d-flex align-center gap-2" style="flex-wrap:wrap;">
            <div>
                <h1 style="font-size:1.85rem;">My <span class="text-gradient">Profile</span></h1>
                <p class="text-muted" style="margin-top:.25rem;">Manage your account and view your activity.</p>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-top:2rem;padding-bottom:4rem;">
    <div class="profile-grid">

        {{-- ─── LEFT: Profile Card ─── --}}
        <div>
            <div class="profile-card">
                <div class="profile-card-banner"></div>
                <div class="profile-avatar-wrap">
                    <img class="profile-avatar" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-email">{{ $user->email }}</div>
                    <div class="profile-role">
                        <span class="badge badge-primary" style="font-size:.75rem;">
                            <i class="fas fa-shield-halved"></i>
                            {{ ucfirst(str_replace('_', ' ', $user->role ?? 'user')) }}
                        </span>
                    </div>
                </div>

                <ul class="profile-meta-list">
                    @if($user->phone)
                    <li class="profile-meta-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ $user->phone }}</span>
                    </li>
                    @endif
                    <li class="profile-meta-item">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $user->email }}</span>
                    </li>
                    <li class="profile-meta-item">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Joined {{ $user->created_at->format('M Y') }}</span>
                    </li>
                    <li class="profile-meta-item">
                        @if($user->google_id)
                            <i class="fab fa-google" style="color:#ea4335;"></i>
                            <span>Signed in with Google</span>
                        @else
                            <i class="fas fa-lock"></i>
                            <span>Password account</span>
                        @endif
                    </li>
                </ul>

                <div class="profile-actions">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary w-full" style="justify-content:center;">
                        <i class="fas fa-pen-to-square"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        {{-- ─── RIGHT: Stats + Activity ─── --}}
        <div>

            {{-- Stats Row --}}
            <div class="stats-row">
                <div class="stat-pill">
                    <div class="stat-pill-val">{{ $stats['events'] }}</div>
                    <div class="stat-pill-label">Events</div>
                </div>
                <div class="stat-pill">
                    <div class="stat-pill-val">{{ $stats['bookings'] }}</div>
                    <div class="stat-pill-label">Bookings</div>
                </div>
                <div class="stat-pill">
                    <div class="stat-pill-val">{{ $stats['venues'] }}</div>
                    <div class="stat-pill-label">Venues</div>
                </div>
                <div class="stat-pill">
                    <div class="stat-pill-val">{{ $stats['pending'] }}</div>
                    <div class="stat-pill-label">Pending</div>
                </div>
            </div>

            {{-- My Events --}}
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="fas fa-calendar-days"></i> My Events
                    </div>
                    <a href="{{ route('events.my') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="section-card-body" style="padding:0 1.5rem;">
                    @forelse($user->events->take(5) as $event)
                    <div class="event-row">
                        <div class="event-date-box">
                            <div class="day">{{ $event->start_datetime->format('d') }}</div>
                            <div class="mon">{{ $event->start_datetime->format('M') }}</div>
                        </div>
                        <div class="event-info">
                            <div class="event-info-title">{{ $event->title }}</div>
                            <div class="event-info-meta">
                                <i class="fas fa-clock"></i> {{ $event->start_datetime->format('h:i A') }}
                                @if($event->venue)
                                    &nbsp;·&nbsp;<i class="fas fa-location-dot"></i> {{ $event->venue->city }}
                                @endif
                            </div>
                        </div>
                        <div>
                            @php
                                $eclass = match($event->status) {
                                    'approved'  => 'badge-success',
                                    'rejected'  => 'badge-danger',
                                    'cancelled' => 'badge-danger',
                                    default     => 'badge-warning',
                                };
                            @endphp
                            <span class="badge {{ $eclass }}">{{ ucfirst($event->status) }}</span>
                        </div>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-calendar-xmark"></i>
                        <p>You haven't created any events yet.</p>
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus"></i> Create Event
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Booking History --}}
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="fas fa-ticket"></i> Booking History
                    </div>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="section-card-body" style="padding:0 1.5rem;">
                    @forelse($user->bookings->take(5) as $booking)
                    <div class="booking-row">
                        <div class="booking-ref">{{ $booking->booking_reference }}</div>
                        <div class="booking-info">
                            @if($booking->event)
                                <div class="booking-info-title">{{ $booking->event->title }}</div>
                                <div class="booking-info-sub">
                                    <i class="fas fa-calendar"></i>
                                    {{ $booking->event->start_datetime->format('d M Y') }}
                                </div>
                            @elseif($booking->venue)
                                <div class="booking-info-title">{{ $booking->venue->name }}</div>
                                <div class="booking-info-sub">
                                    <i class="fas fa-clock"></i>
                                    {{ $booking->start_datetime->format('d M Y') }}
                                </div>
                            @else
                                <div class="booking-info-title">Booking #{{ $booking->id }}</div>
                            @endif
                        </div>
                        <div class="booking-amount">{{ $booking->total_amount_formatted }}</div>
                        <div>
                            @php
                                $bclass = match($booking->status) {
                                    'confirmed'  => 'badge-success',
                                    'cancelled'  => 'badge-danger',
                                    'completed'  => 'badge-info',
                                    default      => 'badge-warning',
                                };
                            @endphp
                            <span class="badge {{ $bclass }}">{{ ucfirst($booking->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-ticket-simple"></i>
                        <p>No bookings yet. Find a venue or event to book!</p>
                        <a href="{{ route('venues.index') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-building"></i> Browse Venues
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </div>
                </div>
                <div class="section-card-body">
                    <div class="grid-2" style="gap:1rem;">
                        <a href="{{ route('venues.create') }}" class="btn btn-secondary" style="justify-content:center;padding:1rem;">
                            <i class="fas fa-building"></i> List a Venue
                        </a>
                        <a href="{{ route('events.create') }}" class="btn btn-secondary" style="justify-content:center;padding:1rem;">
                            <i class="fas fa-calendar-plus"></i> Create Event
                        </a>
                        <a href="{{ route('chat.index') }}" class="btn btn-secondary" style="justify-content:center;padding:1rem;">
                            <i class="fas fa-comments"></i> Live Chat
                        </a>
                        <a href="{{ route('support.index') }}" class="btn btn-secondary" style="justify-content:center;padding:1rem;">
                            <i class="fas fa-headset"></i> Support
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
