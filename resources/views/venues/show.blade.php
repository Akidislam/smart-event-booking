@extends('layouts.app')

@section('title', $venue->name . ' - EventVenue')

@push('styles')
<style>
    .venue-header { margin-top: 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; }
    .venue-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.5rem; font-weight: 800; margin-bottom: .5rem; }
    .venue-meta { display: flex; gap: 1.5rem; color: var(--text-muted); font-size: 1.05rem; }
    .gallery { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; border-radius: var(--radius-lg); overflow: hidden; height: 500px; margin-bottom: 3rem; }
    .gallery-main { width: 100%; height: 100%; object-fit: cover; }
    .gallery-side { display: grid; grid-template-rows: 1fr 1fr; gap: 1rem; height: 100%; }
    .gallery-thumb { width: 100%; height: 100%; object-fit: cover; }
    
    .content-grid { display: grid; grid-template-columns: 1.8fr 1fr; gap: 3rem; margin-bottom: 4rem; }
    .section-title { font-weight: 700; font-size: 1.35rem; margin-bottom: 1.25rem; font-family: 'Plus Jakarta Sans', sans-serif; border-bottom: 1px dashed var(--border-strong); padding-bottom:.5rem; }
    .amenities-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .amenity-item { display: flex; align-items: center; gap: .75rem; background: var(--bg-surface); padding: .75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border); }
    
    .booking-card { background: var(--bg-card); border: 1px solid rgba(99,102,241,0.3); border-radius: var(--radius-lg); padding: 2rem; position: sticky; top: calc(var(--nav-h) + 2rem); box-shadow: 0 20px 40px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1); }
    .price-tag { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.25rem; font-weight: 800; color: #fff; margin-bottom: 1.5rem; display: flex; align-items: baseline; gap: .5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; }
    .price-tag span { font-size: 1.1rem; color: var(--text-muted); font-weight: 500; }
    
    .map-container { width: 100%; height: 400px; border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; margin-top: 2rem; background: var(--bg-surface); }

    @media (max-width: 900px) {
        .gallery { grid-template-columns: 1fr; height: auto; }
        .gallery-side { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr; height: 200px; }
        .content-grid { grid-template-columns: 1fr; }
        .booking-card { position: static; margin-top: 3rem; }
        .venue-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="container pb-5">
    <div class="venue-header">
        <div>
            <div class="badge badge-primary mb-3">{{ \App\Models\Venue::categories()[$venue->category] ?? ucfirst($venue->category) }}</div>
            <h1 class="venue-title">{{ $venue->name }}</h1>
            <div class="venue-meta">
                <span><i class="fas fa-location-dot text-primary"></i> {{ $venue->address }}, {{ $venue->city }}</span>
                <span><i class="fas fa-star text-warning"></i> {{ number_format($venue->rating, 1) }} ({{ $venue->total_reviews ?? 0 }} Reviews)</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            @can('update', $venue)
                <a href="{{ route('venues.edit', $venue) }}" class="btn btn-secondary"><i class="fas fa-pen"></i> Edit Venue</a>
            @endcan
            <button class="btn btn-secondary"><i class="fas fa-share-nodes"></i> Share</button>
            <button class="btn btn-outline" style="border-radius:50%; width:44px; height:44px; padding:0; justify-content:center;"><i class="far fa-heart"></i></button>
        </div>
    </div>

    <!-- Single Image banner -->
    <div class="gallery" style="display:block; height:400px; margin-bottom: 2rem;">
        <img src="{{ $venue->image ? asset('storage/'.$venue->image) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200' }}" alt="Venue Image" style="width:100%; height:100%; object-fit:cover; border-radius: var(--radius-lg);">
    </div>

    <div class="content-grid">
        <!-- Main Description -->
        <div>
            <div class="mb-5">
                <h3 class="section-title">About This Venue</h3>
                <div style="line-height:1.8; color:var(--text-muted); font-size:1.05rem;">
                    {!! nl2br(e($venue->description ?? 'No description provided.')) !!}
                </div>
            </div>

            <div class="mb-5">
                <h3 class="section-title">Amenities & Features</h3>
                <div class="amenities-grid">
                    @php
                        $amenities = $venue->amenities ?? ['WiFi', 'Air Conditioning', 'Projector', 'Sound System', 'Parking'];
                    @endphp
                    @foreach($amenities as $amenity)
                        <div class="amenity-item">
                            <i class="fas fa-check-circle text-success mt-1"></i> <span class="fw-bold fs-sm">{{ $amenity }}</span>
                        </div>
                    @endforeach
                    <div class="amenity-item">
                        <i class="fas fa-users text-primary"></i> <span class="fw-bold fs-sm">Capacity: {{ number_format($venue->capacity) }} People</span>
                    </div>
                </div>
            </div>
            
            <div class="mb-5">
                <h3 class="section-title">Location Map</h3>
                <div class="text-muted"><i class="fas fa-map-pin"></i> {{ $venue->address }}, {{ $venue->city }}, {{ $venue->state }}</div>
                <div class="map-container" id="map">
                    <!-- Google Map will render here. For fallback, simple iframe -->
                    @if($venue->latitude && $venue->longitude)
                        @if(config('services.google.maps_api_key'))
                            <iframe width="100%" height="100%" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q={{ $venue->latitude }},{{ $venue->longitude }}&key={{ config('services.google.maps_api_key') }}" allowfullscreen></iframe>
                        @else
                           <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1a1a35;color:var(--text-muted)">Map credentials not configured</div>
                        @endif
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1a1a35;color:var(--text-muted)">Map coordinates not available</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar / Booking form -->
        <div>
            <div class="booking-card">
                <div class="price-tag">
                    {{ $venue->price_formatted }} <span>/ hour</span>
                </div>
                
                <form action="{{ route('venues.book', $venue) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Booking Start</label>
                        <input type="datetime-local" name="start_datetime" class="form-control" required min="{{ date('Y-m-d\TH:i') }}">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label">Booking End</label>
                        <input type="datetime-local" name="end_datetime" class="form-control" required min="{{ date('Y-m-d\TH:i') }}">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label">Attendees</label>
                        <div class="input-icon">
                            <i class="fas fa-users"></i>
                            <input type="number" name="attendees" class="form-control pl-4" required min="1" max="{{ $venue->capacity }}" value="1">
                        </div>
                    </div>
                    
                    @auth
                        <button type="submit" class="btn btn-primary btn-lg w-full justify-center shadow-lg"><i class="fas fa-calendar-check"></i> Book Venue Now</button>
                        <p class="text-center text-muted mt-3" style="font-size:.8rem;"><i class="fab fa-google"></i> Auto-syncs to Google Calendar</p>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-full justify-center">Log in to book</a>
                    @endauth
                </form>

                <div class="mt-4 pt-4 border-top text-center" style="border-top:1px dashed var(--border-strong);">
                    <p class="fs-sm text-muted mb-2">Hosted by</p>
                    <div class="d-flex align-center justify-center gap-2 mb-2">
                        <img src="{{ $venue->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($venue->user->name) }}" style="width:40px;height:40px;border-radius:50%;" alt="">
                        <div class="fw-bold">{{ $venue->user->name }}</div>
                    </div>
                    <a href="#" class="btn btn-outline btn-sm w-full"><i class="fas fa-comment"></i> Contact Host</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
