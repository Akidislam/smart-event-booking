@extends('layouts.app')

@section('title', 'Admin Dashboard - EventVenue')

@section('content')
<div class="page-header py-4 mb-4" style="background:var(--bg-card); border-bottom:1px solid var(--border-strong);">
    <div class="container d-flex justify-between align-center">
        <div>
            <h1 style="color:var(--warning); margin-bottom:.25rem;"><i class="fas fa-shield-halved"></i> Admin Portal</h1>
            <p class="text-muted m-0">Platform overview and management controls.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users') }}" class="btn btn-secondary"><i class="fas fa-users"></i> Manage Users</a>
            <a href="{{ route('admin.venues') }}" class="btn btn-secondary"><i class="fas fa-building"></i> Manage Venues</a>
        </div>
    </div>
</div>

<div class="container py-4 mb-5">
    
    <!-- Pending Action Alert -->
    @if($stats['pending_venues'] > 0)
    <div class="alert alert-warning mb-4" style="align-items:center;">
        <i class="fas fa-exclamation-triangle" style="font-size:1.5rem;"></i>
        <div style="flex:1;">
            <strong>Action Required: Pending Venues</strong><br>
            There are {{ $stats['pending_venues'] }} venue submissions awaiting admin review.
        </div>
        <a href="{{ route('admin.venues', ['status' => 'pending']) }}" class="btn btn-sm" style="background:#fff;color:#000;">Review Now</a>
    </div>
    @endif

    <!-- High-Level Stats -->
    <div class="grid-4 mb-5">
        <div class="stat-card" style="border-top:3px solid var(--info)">
            <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:var(--info)"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--secondary)">
            <div class="stat-icon" style="background:rgba(236,72,153,0.1);color:var(--secondary)"><i class="fas fa-building"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_venues']) }}</div>
                <div class="stat-label">Active Venues</div>
            </div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--primary-light)">
            <div class="stat-icon" style="background:rgba(99,102,241,0.1);color:var(--primary-light)"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_events']) }}</div>
                <div class="stat-label">Events Hosted</div>
            </div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--success)">
            <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:var(--success)"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="stat-value">৳{{ number_format($stats['total_revenue']) }}</div>
                <div class="stat-label">Total Revenue Processed</div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="grid-2">
        <!-- Recent Users -->
        <div class="card p-0">
            <div style="padding:1.5rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <h3 class="m-0 fw-bold fs-sm text-uppercase text-muted"><i class="fas fa-user-plus text-primary"></i> Newest Registrations</h3>
                <a href="{{ route('admin.users') }}" class="text-primary fs-sm fw-bold" style="text-decoration:none;">View All</a>
            </div>
            <div class="table-wrap">
                <table>
                    <tbody>
                        @foreach($recentUsers as $u)
                        <tr>
                            <td style="width:50px;">
                                <img src="{{ $u->avatar_url }}" style="width:32px;height:32px;border-radius:50%;" alt="">
                            </td>
                            <td>
                                <div class="fw-bold">{{ $u->name }}</div>
                                <div class="text-muted fs-sm">{{ $u->email }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $u->role === 'admin' ? 'badge-danger' : ($u->role === 'venue_owner' ? 'badge-primary' : 'badge-secondary') }}" style="font-size:.7rem;">{{ strtoupper($u->role) }}</span>
                            </td>
                            <td class="text-muted fs-sm text-right" style="padding-right:1.5rem;">{{ $u->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Venues -->
        <div class="card p-0">
            <div style="padding:1.5rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <h3 class="m-0 fw-bold fs-sm text-uppercase text-muted"><i class="fas fa-building text-success"></i> Recent Venue Listings</h3>
                <a href="{{ route('admin.venues') }}" class="text-primary fs-sm fw-bold" style="text-decoration:none;">View All</a>
            </div>
            <div class="table-wrap">
                <table>
                    <tbody>
                        @foreach($recentVenues as $v)
                        <tr>
                            <td>
                                <a href="{{ route('venues.show', $v) }}" target="_blank" class="fw-bold" style="color:var(--text);text-decoration:none;">{{ $v->name }}</a>
                                <div class="text-muted fs-sm"><i class="fas fa-user text-primary" style="font-size:.7rem;"></i> {{ $v->user->name ?? 'Unknown' }}</div>
                            </td>
                            <td>
                                @if($v->status === 'active') <span class="badge badge-success">Active</span>
                                @elseif($v->status === 'pending') <span class="badge badge-warning">Pending</span>
                                @else <span class="badge badge-danger">Inactive</span> @endif
                            </td>
                            <td class="text-muted fs-sm text-right" style="padding-right:1.5rem;">
                                @if($v->status === 'pending')
                                    <form action="{{ route('admin.venues.approve', $v) }}" method="POST" style="display:inline;">@csrf <button class="btn btn-outline btn-sm" style="padding:.2rem .5rem;" title="Approve"><i class="fas fa-check"></i></button></form>
                                @else
                                    {{ $v->created_at->format('M d') }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
