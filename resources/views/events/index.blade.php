@extends('layouts.app')

@section('title', 'Events - EventVenue')

@section('content')
<div class="page-header py-5 mb-4 text-center">
    <div class="container mx-auto px-4">
        <h1>Discover Upcoming <span class="text-gradient">Events</span></h1>
        <p>From tech conferences to local concerts, find what's happening near you.</p>
    </div>
</div>

<div class="container mx-auto px-4 mb-5">
    <div class="d-flex justify-between align-center mb-4" style="flex-wrap:wrap; gap:1rem;">
        <h3 class="fw-bold m-0" style="font-size:1.5rem">Latest Events</h3>
        
        <form action="{{ route('events.index') }}" method="GET" class="d-flex gap-2" style="flex-wrap:wrap;">
            <select name="category" class="form-control" style="width:auto; min-width:200px;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            
            <div class="input-icon" style="width:auto;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control pl-4" placeholder="Search events..." value="{{ request('search') }}" style="min-width:250px;">
            </div>
            <button type="submit" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-filter"></i> Search</button>
        </form>
    </div>

    <!-- Event Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
        <div class="card event-card" style="display:flex; flex-direction:column; height:100%;">
            <!-- Image Header for Events page -->
            <div style="width:100%; height:180px; position:relative;">
                <a href="{{ route('events.show', $event) }}">
                    <img class="w-full h-auto object-cover" src="{{ $event->banner_image ? asset('storage/'.$event->banner_image) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800' }}" alt="{{ $event->title }}" style="width:100%; height:100%; object-fit:cover;">
                </a>
                <div style="position:absolute; top:1rem; left:1rem;" class="d-flex gap-1">
                    <span class="badge badge-primary">{{ \App\Models\Event::categories()[$event->category] ?? $event->category }}</span>
                    @if($event->is_free)<span class="badge badge-success">Free Entry</span>@endif
                </div>
            </div>
            
            <div class="event-body d-flex" style="flex-direction:row; padding:0; flex:1;">
                <div class="event-date-col" style="min-width:75px; flex-shrink:0;">
                    <div class="event-day">{{ $event->start_datetime->format('d') }}</div>
                    <div class="event-month">{{ $event->start_datetime->format('M Y') }}</div>
                </div>
                
                <div style="padding:1.25rem; width:100%; display:flex; flex-direction:column;">
                    <h3 class="event-title mb-2" style="font-size:1.15rem; line-height:1.4;">
                        <a href="{{ route('events.show', $event) }}" style="color:inherit; text-decoration:none;">{{ $event->title }}</a>
                    </h3>
                    <div class="event-meta mb-3" style="flex-direction:column; gap:.5rem;">
                        <span><i class="fas fa-clock text-primary"></i> {{ $event->start_datetime->format('h:i A') }} - {{ $event->end_datetime->format('h:i A') }}</span>
                        @if($event->venue)
                            <span><i class="fas fa-location-dot text-danger"></i> {{ $event->venue->name }}, {{ $event->venue->city }}</span>
                        @endif
                        <span class="text-success fw-bold"><i class="fas fa-ticket-alt"></i> {{ $event->ticket_price_formatted }}</span>
                    </div>
                    
                    <div class="d-flex justify-between align-center mt-auto pt-3 border-top" style="border-top:1px solid var(--border-strong);">
                        <div class="d-flex gap-2">
                            @can('update', $event)
                                <a href="{{ route('events.edit', $event) }}" class="btn btn-outline btn-sm w-full sm:w-auto" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');" style="display:inline-block; margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm text-danger w-full sm:w-auto" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--danger, #dc3545);">Delete</button>
                                </form>
                            @endcan
                        </div>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm w-full sm:w-auto" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Book / View</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="grid-column:1/-1; padding:4rem; text-align:center;">
            <i class="fas fa-calendar-xmark" style="font-size:3rem; color:var(--text-muted); opacity:0.3; margin-bottom:1rem; display:block;"></i>
            <h3 class="mb-2">No events found</h3>
            <p class="text-muted mb-4">Check back later or try adjusting your filters.</p>
            <a href="{{ route('events.index') }}" class="btn btn-primary w-full sm:w-auto">Clear Filters</a>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-center mt-5">
        {{ $events->links() }}
    </div>
</div>
@endsection
