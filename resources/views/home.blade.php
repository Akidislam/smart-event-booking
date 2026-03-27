@extends('layouts.app')

@section('title', 'Smart Event & Venue Booking — Find Your Perfect Space')
@section('meta_description', 'Discover premium event venues, create unforgettable events, and manage bookings effortlessly with Google Calendar sync.')

@push('styles')
<style>
/* ── HERO ── */
.hero {
    min-height: calc(100vh - var(--nav-h));
    display: flex; flex-direction: column; justify-content: center;
    padding: 5rem 0 4rem; position: relative; overflow: hidden;
}
.hero-orb {
    position: absolute; border-radius: 50%;
    filter: blur(80px); pointer-events: none; opacity: .5;
}
.orb-1 { width: 600px; height: 600px; background: rgba(99,102,241,0.25); top: -150px; left: -100px; }
.orb-2 { width: 400px; height: 400px; background: rgba(236,72,153,0.15); bottom: -100px; right: -50px; }
.hero-content { position: relative; z-index: 2; max-width: 860px; }
.hero-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .4rem 1rem; border-radius: 50px;
    background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3);
    color: var(--primary-light); font-size: .8rem; font-weight: 600;
    margin-bottom: 2rem;
    animation: fadeUp .6s ease;
}
.hero h1 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(2.5rem, 6vw, 5rem);
    font-weight: 900; line-height: 1.05; margin-bottom: 1.5rem;
    animation: fadeUp .6s ease .1s both;
}
.hero p {
    font-size: 1.2rem; color: var(--text-muted); max-width: 600px;
    line-height: 1.7; margin-bottom: 2.5rem;
    animation: fadeUp .6s ease .2s both;
}
.hero-actions {
    display: flex; gap: 1rem; flex-wrap: wrap;
    animation: fadeUp .6s ease .3s both;
}
.hero-stats {
    display: flex; gap: 3rem; margin-top: 4rem;
    animation: fadeUp .6s ease .4s both;
}
.hero-stat-value { font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #fff, var(--primary-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hero-stat-label { font-size: .85rem; color: var(--text-muted); margin-top: .1rem; }

/* ── SEARCH BAR ── */
.search-section {
    padding: 0 0 5rem;
    position: relative; z-index: 10;
}
.search-card {
    background: var(--bg-card);
    border: 1px solid var(--border-strong);
    border-radius: var(--radius-lg);
    padding: 2rem; box-shadow: var(--shadow);
    display: grid; grid-template-columns: 1fr 1fr 1fr auto;
    gap: 1rem; align-items: end;
}
.search-field label { display: block; font-size: .75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .5rem; }

/* ── SECTION TITLES ── */
.section-header { text-align: center; margin-bottom: 3.5rem; }
.section-header .eyebrow { font-size: .8rem; font-weight: 700; color: var(--primary-light); text-transform: uppercase; letter-spacing: .1em; display: block; margin-bottom: .75rem; }
.section-header h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: clamp(1.75rem, 3.5vw, 2.75rem); font-weight: 800; margin-bottom: 1rem; }
.section-header p { color: var(--text-muted); font-size: 1.05rem; max-width: 560px; margin: 0 auto; line-height: 1.7; }

/* ── VENUE CARD ── */
.venue-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; transition: var(--transition); cursor: pointer; }
.venue-card:hover { border-color: rgba(99,102,241,0.4); transform: translateY(-6px); box-shadow: 0 30px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(99,102,241,0.1); }
.venue-card-img { position: relative; aspect-ratio: 16/10; overflow: hidden; }
.venue-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s ease; }
.venue-card:hover .venue-card-img img { transform: scale(1.08); }
.venue-card-cat { position: absolute; top: .85rem; left: .85rem; }
.venue-card-price { position: absolute; top: .85rem; right: .85rem; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); color: #fff; padding: .35rem .75rem; border-radius: 50px; font-size: .8rem; font-weight: 700; }
.venue-card-body { padding: 1.25rem; }
.venue-card-name { font-weight: 700; font-size: 1rem; margin-bottom: .4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.venue-card-location { color: var(--text-muted); font-size: .825rem; display: flex; align-items: center; gap: .35rem; margin-bottom: .85rem; }
.venue-card-meta { display: flex; justify-content: space-between; align-items: center; }
.venue-rating { display: flex; align-items: center; gap: .3rem; font-size: .825rem; font-weight: 600; color: var(--accent); }
.venue-capacity { color: var(--text-muted); font-size: .8rem; display: flex; align-items: center; gap: .3rem; }

/* ── EVENT CARD ── */
.event-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; transition: var(--transition); display: flex; }
.event-card:hover { border-color: var(--border-strong); transform: translateY(-4px); box-shadow: var(--shadow); }
.event-date-col { min-width: 80px; background: linear-gradient(160deg, var(--primary), var(--primary-dark)); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.25rem .75rem; }
.event-day { font-size: 2rem; font-weight: 900; line-height: 1; color: #fff; }
.event-month { font-size: .7rem; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,.7); letter-spacing: .05em; }
.event-body { padding: 1.25rem; flex: 1; }
.event-title { font-weight: 700; font-size: .95rem; margin-bottom: .35rem; }
.event-meta { color: var(--text-muted); font-size: .8rem; display: flex; flex-wrap: wrap; gap: .75rem; margin-bottom: .75rem; }
.event-meta span { display: flex; align-items: center; gap: .3rem; }

/* ── HOW IT WORKS ── */
.how-step { text-align: center; padding: 2rem; }
.step-icon { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(236,72,153,0.1)); border: 1px solid rgba(99,102,241,0.3); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1.25rem; color: var(--primary-light); }
.step-num { font-size: .7rem; font-weight: 700; color: var(--primary-light); margin-bottom: .5rem; text-transform: uppercase; letter-spacing: .08em; }
.step-title { font-weight: 700; margin-bottom: .5rem; }
.step-desc { color: var(--text-muted); font-size: .875rem; line-height: 1.6; }

/* ── CTA BANNER ── */
.cta-banner {
    background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(236,72,153,0.1) 100%);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: var(--radius-lg); padding: 4rem;
    text-align: center; position: relative; overflow: hidden;
}
.cta-banner::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse 60% 80% at 50% 50%, rgba(99,102,241,0.08), transparent); pointer-events: none; }
.cta-banner h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; }
.cta-banner p { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem; }

@keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }

@media (max-width: 768px) {
    .search-card { grid-template-columns: 1fr; }
    .hero-stats { gap: 1.5rem; flex-wrap: wrap; }
    .event-card { flex-direction: column; }
    .event-date-col { flex-direction: row; gap: .5rem; padding: .75rem 1.25rem; min-width: unset; }
    .cta-banner { padding: 2rem 1.5rem; }
}
</style>
@endpush

@section('content')
<!-- HERO -->
<section class="hero">
    <div class="hero-orb orb-1"></div>
    <div class="hero-orb orb-2"></div>
    <div class="container mx-auto px-4">
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-star"></i> Bangladesh's #1 Event Booking Platform</div>
            <h1>Find Your <span class="text-gradient">Perfect Venue</span> for Every Event</h1>
            <p>Discover premium venues, create events, and manage bookings seamlessly — all powered by Google Maps and Calendar integration.</p>
            <div class="hero-actions">
                <a href="{{ route('venues.index') }}" class="btn btn-primary btn-lg w-full sm:w-auto"><i class="fas fa-search"></i> Explore Venues</a>
                <a href="{{ route('events.index') }}" class="btn btn-secondary btn-lg w-full sm:w-auto"><i class="fas fa-calendar-days"></i> Browse Events</a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-value">{{ number_format($stats['venues']) }}+</div>
                    <div class="hero-stat-label">Premium Venues</div>
                </div>
                <div>
                    <div class="hero-stat-value">{{ number_format($stats['events']) }}+</div>
                    <div class="hero-stat-label">Active Events</div>
                </div>
                <div>
                    <div class="hero-stat-value">{{ number_format($stats['bookings']) }}+</div>
                    <div class="hero-stat-label">Bookings Made</div>
                </div>
                <div>
                    <div class="hero-stat-value">{{ number_format($stats['users']) }}+</div>
                    <div class="hero-stat-label">Happy Users</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- QUICK SEARCH -->
<section class="search-section">
    <div class="container mx-auto px-4">
        <form action="{{ route('venues.index') }}" method="GET" class="search-card">
            <div class="search-field">
                <label><i class="fas fa-location-dot"></i> City / Location</label>
                <input type="text" name="city" class="form-control" placeholder="e.g. Dhaka, Chittagong..." value="{{ request('city') }}">
            </div>
            <div class="search-field">
                <label><i class="fas fa-tags"></i> Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\Venue::categories() as $key => $label)
                        <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="search-field">
                <label><i class="fas fa-users"></i> Min. Capacity</label>
                <input type="number" name="min_capacity" class="form-control" placeholder="e.g. 50" value="{{ request('min_capacity') }}">
            </div>
            <button type="submit" class="btn btn-primary w-full sm:w-auto" style="height:46px;"><i class="fas fa-magnifying-glass"></i> Search</button>
        </form>
    </div>
</section>

<!-- FEATURED VENUES -->
<section class="section" style="padding-top:0">
    <div class="container mx-auto px-4">
        <div class="section-header">
            <span class="eyebrow">Top Picks</span>
            <h2>Featured <span class="text-gradient">Venues</span></h2>
            <p>Handpicked premium spaces for weddings, conferences, concerts, and everything in between.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredVenues as $venue)
            <div class="venue-card" onclick="location.href='{{ route('venues.show', $venue) }}'">
                <div class="venue-card-img">
                    <img class="w-full h-auto object-cover" src="{{ $venue->first_image }}" alt="{{ $venue->name }}" loading="lazy">
                    <div class="venue-card-cat"><span class="badge badge-primary">{{ \App\Models\Venue::categories()[$venue->category] ?? $venue->category }}</span></div>
                    <div class="venue-card-price">{{ $venue->price_formatted }}/hr</div>
                </div>
                <div class="venue-card-body">
                    <div class="venue-card-name">{{ $venue->name }}</div>
                    <div class="venue-card-location"><i class="fas fa-location-dot"></i> {{ $venue->city }}, {{ $venue->country }}</div>
                    <div class="venue-card-meta">
                        <div class="venue-rating"><i class="fas fa-star"></i> {{ number_format($venue->rating, 1) }} ({{ $venue->total_reviews }})</div>
                        <div class="venue-capacity"><i class="fas fa-users"></i> Up to {{ number_format($venue->capacity) }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-muted);">
                <i class="fas fa-building" style="font-size:3rem;margin-bottom:1rem;opacity:.4;display:block;"></i>
                No venues yet. <a href="{{ route('venues.create') }}" class="text-primary">List the first one!</a>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('venues.index') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-arrow-right"></i> View All Venues</a>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" style="background:linear-gradient(160deg,rgba(99,102,241,0.05) 0%,transparent 60%);">
    <div class="container mx-auto px-4">
        <div class="section-header">
            <span class="eyebrow">Simple Process</span>
            <h2>How It <span class="text-gradient">Works</span></h2>
            <p>Book your perfect venue in four simple steps.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="how-step">
                <div class="step-icon"><i class="fas fa-magnifying-glass"></i></div>
                <div class="step-num">Step 01</div>
                <div class="step-title">Search Venues</div>
                <p class="step-desc">Browse hundreds of premium venues filtered by location, capacity, and category using Google Maps.</p>
            </div>
            <div class="how-step">
                <div class="step-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="step-num">Step 02</div>
                <div class="step-title">Check Availability</div>
                <p class="step-desc">View real-time availability and choose your preferred date and time slot.</p>
            </div>
            <div class="how-step">
                <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                <div class="step-num">Step 03</div>
                <div class="step-title">Book & Pay</div>
                <p class="step-desc">Confirm your booking securely. Get an instant booking reference number.</p>
            </div>
            <div class="how-step">
                <div class="step-icon"><i class="fab fa-google"></i></div>
                <div class="step-num">Step 04</div>
                <div class="step-title">Sync to Calendar</div>
                <p class="step-desc">Your booking is automatically synced to Google Calendar so you never miss it.</p>
            </div>
        </div>
    </div>
</section>

<!-- UPCOMING EVENTS -->
<section class="section">
    <div class="container mx-auto px-4">
        <div class="section-header">
            <span class="eyebrow">What's On</span>
            <h2>Upcoming <span class="text-gradient">Events</span></h2>
            <p>Discover and attend amazing events happening near you.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($upcomingEvents as $event)
            <div class="event-card" onclick="location.href='{{ route('events.show', $event) }}'">
                <div class="event-date-col">
                    <div class="event-day">{{ $event->start_datetime->format('d') }}</div>
                    <div class="event-month">{{ $event->start_datetime->format('M Y') }}</div>
                </div>
                <div class="event-body">
                    <div class="event-title">{{ $event->title }}</div>
                    <div class="event-meta">
                        <span><i class="fas fa-clock"></i> {{ $event->start_datetime->format('h:i A') }}</span>
                        @if($event->venue)
                            <span><i class="fas fa-location-dot"></i> {{ $event->venue->city }}</span>
                        @endif
                        <span><i class="fas fa-tag"></i> {{ $event->ticket_price_formatted }}</span>
                    </div>
                    <div class="d-flex gap-1">
                        <span class="badge badge-primary">{{ \App\Models\Event::categories()[$event->category] ?? $event->category }}</span>
                        @if($event->is_free)<span class="badge badge-success">Free</span>@endif
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-muted);">
                <i class="fas fa-calendar-xmark" style="font-size:3rem;margin-bottom:1rem;opacity:.4;display:block;"></i>
                No upcoming events. <a href="{{ route('events.create') }}" class="text-primary">Create one!</a>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('events.index') }}" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-arrow-right"></i> View All Events</a>
        </div>
    </div>
</section>

<!-- CTA BANNER -->
<section class="section-sm">
    <div class="container mx-auto px-4">
        <div class="cta-banner">
            <h2>Ready to List Your <span class="text-gradient">Venue?</span></h2>
            <p>Join hundreds of venue owners and start earning by hosting amazing events.</p>
            <div class="d-flex gap-2 justify-center" style="flex-wrap:wrap;">
                @auth
                    <a href="{{ route('venues.create') }}" class="btn btn-primary btn-lg w-full sm:w-auto"><i class="fas fa-plus"></i> List Your Venue</a>
                    <a href="{{ route('events.create') }}" class="btn btn-secondary btn-lg w-full sm:w-auto"><i class="fas fa-calendar-plus"></i> Create an Event</a>
                @else
                    <a href="{{ route('auth.google') }}" class="btn btn-primary btn-lg w-full sm:w-auto"><i class="fab fa-google"></i> Get Started with Google</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary btn-lg w-full sm:w-auto"><i class="fas fa-user-plus"></i> Create Free Account</a>
                @endauth
            </div>
        </div>
    </div>
</section>
@endsection
