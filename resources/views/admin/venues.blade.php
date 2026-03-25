@extends('layouts.app')

@section('title', 'Manage Venues - Admin Portal')

@section('content')
<div class="page-header py-4 mb-4" style="background:var(--bg-card); border-bottom:1px solid var(--border-strong);">
    <div class="container d-flex justify-between align-center">
        <div>
            <h1 style="color:var(--warning); margin-bottom:.25rem;"><i class="fas fa-building text-success"></i> Venue Approvals</h1>
            <p class="text-muted m-0">Review pending venue listings and manage platform inventory.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<div class="container mb-5">
    
    <div class="d-flex justify-between align-center mb-4">
        <ul class="nav-links" style="background:var(--bg-card); padding:.25rem; border-radius:var(--radius); border:1px solid var(--border); width:max-content;">
            <li><a href="{{ route('admin.venues') }}" class="{{ !request('status') ? 'active' : '' }}" style="padding:.5rem 1.5rem; border-radius:var(--radius-sm); font-weight:600;"><i class="fas fa-list"></i> All</a></li>
            <li><a href="{{ route('admin.venues', ['status' => 'pending']) }}" class="{{ request('status') === 'pending' ? 'active' : '' }}" style="padding:.5rem 1.5rem; border-radius:var(--radius-sm); font-weight:600; color:var(--warning);"><i class="fas fa-clock"></i> Pending Reviews</a></li>
            <li><a href="{{ route('admin.venues', ['status' => 'active']) }}" class="{{ request('status') === 'active' ? 'active' : '' }}" style="padding:.5rem 1.5rem; border-radius:var(--radius-sm); font-weight:600; color:var(--success);"><i class="fas fa-check-circle"></i> Active</a></li>
        </ul>
        <span class="fs-sm text-muted">Showing {{ $venues->total() }} Venues</span>
    </div>

    <div class="card p-0">
        <div class="table-wrap">
            <table style="width:100%; border-collapse:collapse;">
                <thead style="background:var(--bg-surface);">
                    <tr>
                        <th style="padding:1rem 1.5rem;">Venue Info</th>
                        <th>Owner</th>
                        <th>Location & Specs</th>
                        <th>Pricing</th>
                        <th>Status</th>
                        <th style="text-align:right; padding-right:1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($venues as $v)
                    <tr>
                        <td style="padding:1.5rem;">
                            <div class="d-flex align-center gap-3">
                                <img src="{{ $v->first_image }}" style="width:60px; height:45px; border-radius:6px; object-fit:cover; border:1px solid var(--border);" alt="{{ $v->name }}">
                                <div>
                                    <a href="{{ route('venues.show', $v) }}" target="_blank" class="fw-bold fs-sm text-primary" style="text-decoration:none;">{{ $v->name }}</a>
                                    <div class="text-muted" style="font-size:.7rem; margin-top:.25rem;"><i class="fas fa-tag"></i> {{ \App\Models\Venue::categories()[$v->category] ?? $v->category }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <img src="{{ $v->user->avatar_url }}" style="width:24px; height:24px; border-radius:50%;" alt="">
                                <div>
                                    <div class="fw-bold fs-sm">{{ $v->user->name ?? 'Unknown' }}</div>
                                    <a href="mailto:{{ $v->user->email ?? '' }}" class="text-muted" style="font-size:.7rem; text-decoration:none;"><i class="fas fa-envelope"></i> Contact</a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fs-sm mb-1"><i class="fas fa-map-marker-alt text-danger"></i> {{ $v->city }}</div>
                            <div class="text-muted" style="font-size:.75rem;"><i class="fas fa-users text-primary"></i> Cap: {{ $v->capacity }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-success">{{ $v->price_formatted }} <span class="text-muted" style="font-size:.75rem;">/hr</span></div>
                        </td>
                        <td>
                            @if($v->status === 'active') <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                            @elseif($v->status === 'pending') <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
                            @else <span class="badge badge-danger"><i class="fas fa-ban"></i> Rejected</span> @endif
                        </td>
                        <td style="text-align:right; padding-right:1.5rem; vertical-align:middle;">
                            <div class="d-flex justify-between gap-1" style="justify-content:flex-end;">
                                <a href="{{ route('venues.show', $v) }}" target="_blank" class="btn btn-outline btn-sm" title="Preview"><i class="fas fa-eye"></i></a>
                                
                                @if($v->status === 'pending')
                                    <form action="{{ route('admin.venues.approve', $v) }}" method="POST">@csrf <button class="btn btn-sm" style="background:var(--success); color:#fff;" title="Approve"><i class="fas fa-check"></i></button></form>
                                    <form action="{{ route('admin.venues.reject', $v) }}" method="POST">@csrf <button class="btn btn-sm" style="background:var(--danger); color:#fff;" title="Reject"><i class="fas fa-times"></i></button></form>
                                @endif
                                
                                @if($v->status === 'active')
                                    <form action="{{ route('admin.venues.reject', $v) }}" method="POST" onsubmit="return confirm('Suspend this venue?');">@csrf <button class="btn btn-outline btn-sm border-danger text-danger" title="Suspend"><i class="fas fa-ban"></i></button></form>
                                @endif
                                
                                <form action="{{ route('venues.destroy', $v) }}" method="POST" onsubmit="return confirm('Irreversibly delete this venue?');">@csrf @method('DELETE') <button class="btn btn-outline btn-sm" style="color:var(--text-muted); border-color:transparent;" title="Delete forever"><i class="fas fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-building mb-3" style="font-size:2.5rem;opacity:0.3;display:block;"></i> No venues currently match your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-center mt-4">
        {{ $venues->links() }}
    </div>
</div>
@endsection
