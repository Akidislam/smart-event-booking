<div class="d-flex justify-between align-center mb-4">
    <h3 class="fw-bold fs-sm text-muted">Showing {{ $venues->firstItem() ?? 0 }} - {{ $venues->lastItem() ?? 0 }} out of {{ $venues->total() }} venues</h3>
    <div class="d-flex gap-2">
        <button class="btn btn-secondary btn-sm" onclick="document.querySelector('.filters-card').style.display = 'block'"><i class="fas fa-filter"></i> Filters</button>
    </div>
</div>

@php
    $activeFilters = [];
    if(request('search')) $activeFilters['search'] = 'Search: '.request('search');
    if(request('location')) $activeFilters['location'] = 'Location: '.request('location');
    if(request('category')) $activeFilters['category'] = 'Category: '.(App\Models\Venue::categories()[request('category')] ?? request('category'));
    if(request('date')) $activeFilters['date'] = 'Date: '.request('date');
    if(request('capacity')) $activeFilters['capacity'] = 'Min Capacity: '.request('capacity');
    if(request('min_price') || request('max_price')) {
        $priceFormat = 'Price: ';
        if(request('min_price')) $priceFormat .= 'Min '.request('min_price'). ' ';
        if(request('max_price')) $priceFormat .= 'Max '.request('max_price');
        $activeFilters['price'] = trim($priceFormat);
    }
@endphp

@if(count($activeFilters) > 0)
<div class="active-filters mb-4 d-flex gap-2 flex-wrap align-center">
    <span class="text-muted fs-sm fw-bold">Active:</span>
    @foreach($activeFilters as $key => $filter)
        <span class="badge" style="background:var(--bg-card); color:var(--text); border:1px solid var(--border); padding:0.4rem 0.75rem; border-radius:50px; font-weight:normal; display:inline-flex; align-items:center; gap:0.5rem;">
            {{ $filter }}
            <i class="fas fa-times text-muted" style="cursor:pointer;" onclick="removeFilter('{{ $key }}')"></i>
        </span>
    @endforeach
    <button type="button" class="btn btn-link text-muted btn-sm p-0 ms-2" onclick="resetFilters()">Clear all</button>
</div>
@endif

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
        <h3 class="mb-2">No results found</h3>
        <p class="text-muted mb-4">Try adjusting your filters or search query.</p>
        <button type="button" class="btn btn-primary" onclick="resetFilters()">Clear Filters</button>
    </div>
    @endforelse
</div>

<div class="d-flex justify-center mt-5 pagination-container">
    {{ $venues->appends(request()->query())->links() }}
</div>
