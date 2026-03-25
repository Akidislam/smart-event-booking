@extends('layouts.app')

@section('title', 'Dashboard - EventVenue')

@section('content')
<div class="page-header pb-4 mb-4">
    <div class="container">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->name }}!</p>
    </div>
</div>

<div class="container py-4 mb-5">
    <div class="grid-4 mb-5">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.1);color:var(--primary-light)"><i class="fas fa-ticket"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['bookings']) }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(236,72,153,0.1);color:var(--secondary)"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['events']) }}</div>
                <div class="stat-label">Events Created</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:var(--success)"><i class="fas fa-building"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['venues']) }}</div>
                <div class="stat-label">Venues Listed</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:var(--warning)"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="stat-value">৳{{ number_format($stats['spent']) }}</div>
                <div class="stat-label">Total Spent</div>
            </div>
        </div>
    </div>

    <div class="grid-2" style="grid-template-columns: 1.5fr 1fr;">
        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-body" style="padding:1.5rem 1.75rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:1.15rem; font-weight:700;"><i class="fas fa-clock text-primary"></i> Recent Bookings</h3>
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Details</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myBookings as $booking)
                        <tr>
                            <td class="fw-bold fs-sm">{{ $booking->booking_reference }}</td>
                            <td>
                                @if($booking->event)
                                    <div class="fw-bold">{{ $booking->event->title }}</div>
                                    <div class="text-muted fs-sm">Event Ticket ({{ $booking->attendees }} pax)</div>
                                @else
                                    <div class="fw-bold">{{ $booking->venue->name }}</div>
                                    <div class="text-muted fs-sm">Venue Booking</div>
                                @endif
                            </td>
                            <td class="fs-sm">{{ $booking->start_datetime->format('d M, Y') }}</td>
                            <td>{!! $booking->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No recent bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Links / Profile -->
        <div class="d-flex" style="flex-direction:column; gap:1.5rem;">
            <div class="card">
                <div class="card-body text-center py-4">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="mb-3" style="width:80px;height:80px;border-radius:50%;border:3px solid var(--primary);object-fit:cover;">
                    <h3 style="margin-bottom:.25rem;">{{ auth()->user()->name }}</h3>
                    <p class="text-muted mb-3 fs-sm">{{ auth()->user()->email }}</p>
                    <div class="badge badge-primary">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body" style="padding:1.5rem 1.75rem; border-bottom:1px solid var(--border);">
                    <h3 style="font-size:1.15rem; font-weight:700;">Quick Actions</h3>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:.75rem;">
                    <a href="{{ route('venues.create') }}" class="btn btn-outline w-full justify-between"><span><i class="fas fa-plus-circle"></i> List New Venue</span> <i class="fas fa-arrow-right"></i></a>
                    <a href="{{ route('events.create') }}" class="btn btn-outline w-full justify-between"><span><i class="fas fa-calendar-plus"></i> Create Event</span> <i class="fas fa-arrow-right"></i></a>
                    <a href="{{ route('venues.index') }}" class="btn btn-secondary w-full justify-between"><span><i class="fas fa-search"></i> Find Venues</span> <i class="fas fa-arrow-right"></i></a>
                    @if(!auth()->user()->google_token)
                        <div class="alert alert-warning mb-0 mt-2 p-3">
                            <div><strong>Connect Calendar</strong><br>Connect Google to auto-sync bookings to your calendar.</div>
                            <a href="{{ route('auth.google') }}" class="btn btn-sm" style="background:#fff;color:#000;margin-top:.75rem;">Connect</a>
                        </div>
                    @else
                        <div class="alert alert-success mb-0 mt-2 p-3" style="align-items:center;">
                            <i class="fab fa-google" style="font-size:1.25rem;"></i> Google Calendar is synced
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
