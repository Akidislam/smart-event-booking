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
    <div class="container mx-auto px-4">
        <h1>Discover Premium <span class="text-gradient">Venues</span></h1>
        <p>Find the perfect location for your next big event from our curated collection.</p>
    </div>
</div>

<div class="container mx-auto px-4 mb-5">
    <div class="venue-grid">
        <!-- Main Listing -->
        <div id="venues-listing-container">
            @include('venues.partials.listing')
        </div>

        <!-- Sidebar Filters -->
        <!-- Sidebar Filters -->
        <form action="{{ route('venues.index') }}" method="GET" class="filters-card" id="filter-form">
            <h3 class="fw-bold mb-4" style="font-size:1.2rem; border-bottom: 1px solid var(--border); padding-bottom:.75rem;">
                <i class="fas fa-sliders-h text-primary"></i> Filter Venues
            </h3>

            <div class="filter-group">
                <div class="filter-title">Search</div>
                <input type="text" name="search" class="filter-input" placeholder="Venue name or keywords" value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <div class="filter-title">Location</div>
                <div class="input-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <select name="location" class="filter-input pl-4" style="appearance: auto;">
                        <option value="">All over Bangladesh</option>
                        <option value="Dhaka" {{ request('location') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                        <option value="Gazipur" {{ request('location') == 'Gazipur' ? 'selected' : '' }}>Gazipur</option>
                        <option value="Chittagong" {{ request('location') == 'Chittagong' ? 'selected' : '' }}>Chittagong</option>
                        <option value="Sylhet" {{ request('location') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                        <option value="Rajshahi" {{ request('location') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                        <option value="Khulna" {{ request('location') == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                        <option value="Barisal" {{ request('location') == 'Barisal' ? 'selected' : '' }}>Barisal</option>
                    </select>
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
                <div class="filter-title">Event Date</div>
                <div class="input-icon">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="date" class="filter-input pl-4" value="{{ request('date') }}">
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-title">Minimum Capacity</div>
                <input type="number" name="capacity" class="filter-input" placeholder="e.g. 50" value="{{ request('capacity') }}">
            </div>

            <div class="filter-group">
                <div class="filter-title">Price Range (Hourly)</div>
                <div class="d-flex gap-2 mb-2">
                    <input type="number" name="min_price" class="filter-input" placeholder="Min Price" value="{{ request('min_price') }}">
                </div>
                <div class="d-flex gap-2">
                    <input type="number" name="max_price" class="filter-input" placeholder="Max Price" value="{{ request('max_price') }}">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary w-full justify-center w-full sm:w-auto" onclick="resetFilters()">Reset</button>
                <button type="submit" class="btn btn-primary w-full justify-center w-full sm:w-auto">Apply Filters</button>
            </div>
        </form>
    </div>

@push('scripts')
<script>
    const filterForm = document.getElementById('filter-form');
    const listingContainer = document.getElementById('venues-listing-container');
    let timeoutId;

    filterForm.addEventListener('input', function(e) {
        if (e.target.type !== 'submit' && e.target.type !== 'button') {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                fetchVenues();
            }, 500); // Debounce
        }
    });

    filterForm.addEventListener('change', function(e) {
        if (e.target.tagName === 'SELECT' || e.target.type === 'radio' || e.target.type === 'date') {
            fetchVenues();
        }
    });

    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchVenues();
    });

    function resetFilters() {
        filterForm.reset();
        
        // Ensure manual clearing of inputs to bypass cached states
        Array.from(filterForm.elements).forEach(el => {
            if(el.type === 'text' || el.type === 'number' || el.type === 'date') el.value = '';
            if(el.type === 'radio' && el.value === '') el.checked = true;
            if(el.type === 'select-one') el.selectedIndex = 0;
        });
        
        fetchVenues();
    }

    // Export resetting function for usage in the empty state
    window.resetFilters = resetFilters;

    window.removeFilter = function(filterKey) {
        if (filterKey === 'price') {
            document.querySelector('input[name="min_price"]').value = '';
            document.querySelector('input[name="max_price"]').value = '';
        } else if (filterKey === 'category') {
            const defaultRadio = document.querySelector('input[name="category"][value=""]');
            if(defaultRadio) defaultRadio.checked = true;
        } else {
            const el = document.querySelector(`[name="${filterKey}"]`);
            if(el) {
                if(el.type === 'radio') {
                    document.querySelector(`input[name="${filterKey}"][value=""]`).checked = true;
                } else {
                    el.value = '';
                }
            }
        }
        fetchVenues();
    };

    function fetchVenues(url = null) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        let requestUrl = url || `{{ route('venues.index') }}?${params.toString()}`;

        // Update URL
        window.history.pushState({}, '', requestUrl);

        // Show loading state
        listingContainer.style.opacity = '0.5';

        fetch(requestUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            listingContainer.innerHTML = html;
            listingContainer.style.opacity = '1';
            
            // Re-attach pagination handlers since HTML changed
            attachPaginationHandlers();
        })
        .catch(error => {
            console.error('Error fetching venues:', error);
            listingContainer.style.opacity = '1';
        });
    }

    function attachPaginationHandlers() {
        const links = listingContainer.querySelectorAll('.pagination a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchVenues(this.href);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        attachPaginationHandlers();
    });
</script>
@endpush
</div>
@endsection
