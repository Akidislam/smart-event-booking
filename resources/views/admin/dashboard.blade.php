@extends('layouts.app')

@section('title', 'Admin Dashboard - EventVenue')

@section('content')
<div class="page-header py-4 mb-4" style="background:var(--bg-card); border-bottom:1px solid var(--border-strong);">
    <div class="container mx-auto px-4 d-flex justify-between align-center">
        <div>
            <h1 style="color:var(--warning); margin-bottom:.25rem;"><i class="fas fa-shield-halved"></i> Admin Portal</h1>
            <p class="text-muted m-0">Platform overview and management controls.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-users"></i> Manage Users</a>
            <a href="{{ route('admin.venues') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-building"></i> Manage Venues</a>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-4 mb-5">
    
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

    <!-- High-Level Stats (Users, Bookings, Revenue) -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1.5rem;margin-bottom:2rem;">
        <div class="stat-card" style="border-top:3px solid var(--info);background:var(--bg-card);border-radius:var(--radius);padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;align-items:center;gap:1.5rem;border-left:1px solid var(--border);border-right:1px solid var(--border);border-bottom:1px solid var(--border);">
            <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:var(--info);width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-value" style="font-size:1.75rem;font-weight:700;line-height:1.2;">{{ number_format($stats['total_users']) }}</div>
                <div class="stat-label" style="color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;font-size:0.75rem;">Total Users</div>
            </div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--primary-light);background:var(--bg-card);border-radius:var(--radius);padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;align-items:center;gap:1.5rem;border-left:1px solid var(--border);border-right:1px solid var(--border);border-bottom:1px solid var(--border);">
            <div class="stat-icon" style="background:rgba(99,102,241,0.1);color:var(--primary-light);width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="fas fa-ticket"></i></div>
            <div>
                <div class="stat-value" style="font-size:1.75rem;font-weight:700;line-height:1.2;">{{ number_format($stats['total_bookings']) }}</div>
                <div class="stat-label" style="color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;font-size:0.75rem;">Total Bookings</div>
            </div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--success);background:var(--bg-card);border-radius:var(--radius);padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;align-items:center;gap:1.5rem;border-left:1px solid var(--border);border-right:1px solid var(--border);border-bottom:1px solid var(--border);">
            <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:var(--success);width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="stat-value" style="font-size:1.75rem;font-weight:700;line-height:1.2;">৳{{ number_format($stats['total_revenue']) }}</div>
                <div class="stat-label" style="color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;font-size:0.75rem;">Total Revenue</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(400px, 1fr));gap:1.5rem;margin-bottom:2rem;">
        <!-- Bar Chart -->
        <div class="card p-0" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);">
            <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border);">
                <h3 class="m-0 fw-bold fs-sm text-uppercase text-muted"><i class="fas fa-chart-bar text-primary"></i> System Overview</h3>
            </div>
            <div style="padding:1.5rem;">
                <canvas id="overviewChart" height="280"></canvas>
            </div>
        </div>
        
        <!-- Line Chart -->
        <div class="card p-0" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);">
            <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border);">
                <h3 class="m-0 fw-bold fs-sm text-uppercase text-muted"><i class="fas fa-chart-line text-success"></i> Monthly Bookings Trend</h3>
            </div>
            <div style="padding:1.5rem;">
                <canvas id="monthlyChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                <img class="w-full h-auto object-cover w-full h-auto object-cover" src="{{ $u->avatar_url }}" style="width:32px;height:32px;border-radius:50%;" alt="">
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
                                    <form action="{{ route('admin.venues.approve', $v) }}" method="POST" style="display:inline;">@csrf <button class="btn btn-outline btn-sm w-full sm:w-auto" style="padding:.2rem .5rem;" title="Approve"><i class="fas fa-check"></i></button></form>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Overview Bar Chart
    const ctxOverview = document.getElementById('overviewChart').getContext('2d');
    new Chart(ctxOverview, {
        type: 'bar',
        data: {
            labels: ['Users', 'Bookings', 'Revenue'],
            datasets: [{
                label: 'Total Stats',
                data: [
                    {{ $stats['total_users'] }}, 
                    {{ $stats['total_bookings'] }}, 
                    {{ $stats['total_revenue'] }}
                ],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)', // blue (users)
                    'rgba(99, 102, 241, 0.7)', // indigo (bookings)
                    'rgba(16, 185, 129, 0.7)'  // green (revenue)
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(99, 102, 241, 1)',
                    'rgba(16, 185, 129, 1)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.dataIndex === 2) {
                                label += '৳' + context.raw;
                            } else {
                                label += context.raw;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // 2. Monthly Bookings Line Chart
    @php
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyData = [];
        for($i=1; $i<=12; $i++) {
            $monthlyData[] = $monthlyBookings[$i] ?? 0;
        }
    @endphp

    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Bookings Count',
                data: {!! json_encode($monthlyData) !!},
                borderColor: 'rgba(99, 102, 241, 1)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { precision: 0 } // whole numbers only
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
