@extends('layouts.app')

@section('title', 'Browser Venues - EventVenue')

@push('styles')
<style>
    .venue-grid { display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: start; }
    .filters-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; position: sticky; top: calc(var(--nav-h) + 1.5rem); }
    .filter-group { margin-bottom: 1.25rem; border-bottom: 1px solid var(--border); padding-bottom: 1.25rem; }
    .filter-group:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
    .filter-title { font-weight: 700; margin-bottom: 1rem; color: var(--text); }
    .filter-input { width: 100%; padding: .6rem .85rem; background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text); transition: var(--transition); }
    .filter-input:focus { border-color: var(--primary); outline: none; }
    .venue-listing { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    
    @media (max-width: 992px) {
        .venue-grid { grid-template-columns: 1fr; }
        .filters-card { display: none; } /* Could add a mobile toggle button */
        .venue-listing { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="page-header py-5 mb-4 text-center">
    <div class="container">
        <h1>Discover Premium <span class="text-gradient">Venues</span></h1>
        <p>Find the perfect location for your next big event from our curated collection.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="venue-grid">
        <!-- Main Listing -->
        <div>
            <div class="d-flex justify-between align-center mb-4">
                <h3 class="fw-bold fs-sm text-muted">Showing {{ $venues->firstItem() ?? 0 }} - {{ $venues->lastItem() ?? 0 }} out of {{ $venues->total() }} venues</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary btn-sm" onclick="document.querySelector('.filters-card').style.display = 'block'"><i class="fas fa-filter"></i> Filters</button>
                </div>
            </div>

            <div class="venue-listing mb-5">
                @forelse($venues as $venue)
                <div class="card" style="transition:var(--transition)">
                    <div style="position:relative; aspect-ratio:16/10; overflow:hidden; border-bottom:1px solid var(--border)">
                        <a href="{{ route('venues.show', $venue) }}">
                            <img src="{{ $venue->first_image }}" alt="{{ $venue->name }}" style="width:100%; height:100%; object-fit:cover;">
                        </a>
                        <span class="badge badge-primary" style="position:absolute; top:1rem; left:1rem;">{{ \App\Models\Venue::categories()[$venue->category] ?? $venue->category }}</span>
                        <div style="position:absolute; bottom:1rem; right:1rem; background:rgba(0,0,0,0.8); backdrop-filter:blur(4px); padding:.35rem .75rem; border-radius:50px; font-weight:700; color:#fff;">{{ $venue->price_formatted }}/hr</div>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-between align-center mb-1">
                            <h4 class="fw-bold" style="font-size:1.1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <a href="{{ route('venues.show', $venue) }}" style="color:inherit; text-decoration:none;">{{ $venue->name }}</a>
                            </h4>
                            <div class="text-warning fw-bold fs-sm"><i class="fas fa-star"></i> {{ number_format($venue->rating, 1) }}</div>
                        </div>
                        <div class="text-muted fs-sm mb-3"><i class="fas fa-location-dot"></i> {{ $venue->address }}, {{ $venue->city }}</div>
                        <div class="d-flex justify-between align-center border-top pt-3" style="border-top:1px solid var(--border-strong);">
                            <div class="text-muted fs-sm"><i class="fas fa-users text-primary"></i> Cap: {{ $venue->capacity }} pax</div>
                            <div class="d-flex gap-2">
                                @can('update', $venue)
                                    <a href="{{ route('venues.edit', $venue) }}" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                    <form action="{{ route('venues.destroy', $venue) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this venue?');" style="display:inline-block; margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm text-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--danger, #dc3545);">Delete</button>
                                    </form>
                                @endcan
                                <a href="{{ route('venues.show', $venue) }}" class="btn btn-primary btn-sm" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="card" style="grid-column:1/-1; padding:4rem; text-align:center;">
                    <i class="fas fa-search" style="font-size:3rem; color:var(--text-muted); opacity:0.3; margin-bottom:1rem; display:block;"></i>
                    <h3 class="mb-2">No venues found</h3>
                    <p class="text-muted mb-4">Try adjusting your filters or search query.</p>
                    <a href="{{ route('venues.index') }}" class="btn btn-primary">Clear Filters</a>
                </div>
                @endforelse
            </div>

            <div class="d-flex justify-center mt-5">
                {{ $venues->links() }}
            </div>
        </div>

        <!-- Sidebar Filters -->
        <form action="{{ route('venues.index') }}" method="GET" class="filters-card">
            <h3 class="fw-bold mb-4" style="font-size:1.2rem; border-bottom: 1px solid var(--border); padding-bottom:.75rem;">
                <i class="fas fa-sliders H text-primary"></i> Filter Venues
            </h3>

            <div class="filter-group">
                <div class="filter-title">Search</div>
                <input type="text" name="search" class="filter-input" placeholder="Venue name or keywords" value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <div class="filter-title">Location</div>
                <div class="input-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="city" class="filter-input pl-4" placeholder="City or region" value="{{ request('city') }}">
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-title">Category</div>
                <div class="d-flex gap-2" style="flex-direction:column;">
                    <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer;" class="fs-sm text-muted">
                        <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} style="accent-color:var(--primary)"> All Categories
                    </label>
                    @foreach($categories as $key => $label)
                    <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer;" class="fs-sm text-muted">
                        <input type="radio" name="category" value="{{ $key }}" {{ request('category') === $key ? 'checked' : '' }} style="accent-color:var(--primary)"> {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-title">Minimum Capacity</div>
                <input type="number" name="min_capacity" class="filter-input" placeholder="e.g. 50" value="{{ request('min_capacity') }}">
            </div>

            <div class="filter-group">
                <div class="filter-title">Max Price (Hourly)</div>
                <div class="input-icon">
                    <i class="fas fa-tags"></i>
                    <input type="number" name="max_price" class="filter-input pl-4" placeholder="e.g. 5000" value="{{ request('max_price') }}">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('venues.index') }}" class="btn btn-secondary w-full justify-center">Reset</a>
                <button type="submit" class="btn btn-primary w-full justify-center">Apply Filters</button>
            </div>
        </form>
    </div>
</div>
@endsection
