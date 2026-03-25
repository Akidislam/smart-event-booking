@extends('layouts.app')

@section('title', 'My Venues - EventVenue')

@section('content')
<div class="page-header py-4 mb-4">
    <div class="container d-flex justify-between align-center">
        <div>
            <h1>My <span class="text-gradient">Venues</span></h1>
            <p>Manage your properties, view bookings, and update availability.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="{{ route('venues.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> List New Venue</a>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="grid-3 mb-5">
        @forelse($venues as $v)
        <div class="card p-0" style="display:flex; flex-direction:column; overflow:hidden;">
            <div style="position:relative; aspect-ratio:16/9; width:100%;">
                <img src="{{ $v->first_image }}" style="width:100%; height:100%; object-fit:cover;" alt="">
                @if($v->status === 'active') <span class="badge badge-success" style="position:absolute; top:1rem; right:1rem; backdrop-filter:blur(4px);">Active</span>
                @elseif($v->status === 'pending') <span class="badge badge-warning" style="position:absolute; top:1rem; right:1rem; backdrop-filter:blur(4px);">Pending Review</span>
                @else <span class="badge badge-danger" style="position:absolute; top:1rem; right:1rem; backdrop-filter:blur(4px);">Suspended</span> @endif
            </div>
            
            <div class="p-3" style="flex:1;">
                <h3 class="fw-bold mb-1" style="font-size:1.1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $v->name }}">{{ $v->name }}</h3>
                <div class="text-muted fs-sm mb-3"><i class="fas fa-location-dot"></i> {{ $v->address }}, {{ $v->city }}</div>
                
                <div class="d-flex justify-between align-center border-top border-bottom py-2 my-3" style="border-color:var(--border);">
                    <div class="text-center">
                        <div class="fs-sm text-muted mb-1 text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:1px;">Total Bookings</div>
                        <div class="fw-bold text-primary">{{ $v->bookings_count }}</div>
                    </div>
                    <div style="width:1px; height:24px; background:var(--border);"></div>
                    <div class="text-center">
                        <div class="fs-sm text-muted mb-1 text-uppercase fw-bold" style="font-size:.65rem;letter-spacing:1px;">Rating</div>
                        <div class="fw-bold text-warning"><i class="fas fa-star" style="font-size:.8rem;"></i> {{ number_format($v->rating, 1) }}</div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid" style="grid-template-columns:1fr 1fr; border-top:1px solid var(--border);">
                <a href="{{ route('venues.edit', $v) }}" class="btn" style="border-radius:0; justify-content:center; padding:1rem; background:var(--bg-surface); color:var(--text); border-right:1px solid var(--border);"><i class="fas fa-pen"></i> Edit Detail</a>
                <a href="{{ route('venues.show', $v) }}" target="_blank" class="btn" style="border-radius:0; justify-content:center; padding:1rem; background:var(--bg-surface); color:var(--text);"><i class="fas fa-external-link-alt"></i> View Page</a>
            </div>
        </div>
        @empty
        <div class="card" style="grid-column:1/-1; padding:5rem; text-align:center;">
            <i class="fas fa-house-chimney text-muted mb-3" style="font-size:3rem; opacity:0.3;"></i>
            <h3 class="fw-bold mb-2">You haven't listed any venues yet</h3>
            <p class="text-muted mb-4">Turn your unused space into earnings. List a venue today!</p>
            <a href="{{ route('venues.create') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus-circle"></i> Create Your First Listing</a>
        </div>
        @endforelse
    </div>
    
    @if(count($venues) > 0)
    <div class="d-flex justify-center">
        {{ $venues->links() }}
    </div>
    @endif
</div>
@endsection
