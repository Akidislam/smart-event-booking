@extends('layouts.app')

@section('title', 'Food Menu — EventVenue')
@section('meta_description', 'Explore our premium food menu categories. Choose from Chinese, Deshi, Continental and more for your events.')

@section('content')
<div class="page-header" style="text-align:center;">
    <div class="container">
        <h1><i class="fas fa-utensils text-gradient"></i> Our Food Menu</h1>
        <p style="max-width:600px;margin:0 auto;">Browse our curated food categories and select the perfect dishes for your event</p>
    </div>
</div>

<div class="container" style="padding-bottom:5rem;">
    @if($categories->count())
    <div class="grid-3">
        @foreach($categories as $category)
        <a href="{{ route('menu.show', $category) }}" class="card menu-category-card" style="text-decoration:none;color:inherit;">
            <div style="height:180px;background:linear-gradient(135deg, {{ ['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ef4444','#14b8a6'][$loop->index % 8] }}22, {{ ['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ef4444','#14b8a6'][$loop->index % 8] }}08);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                <div style="font-size:4rem;opacity:.15;position:absolute;">
                    <i class="{{ $category->icon ?: 'fas fa-utensils' }}"></i>
                </div>
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg, {{ ['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ef4444','#14b8a6'][$loop->index % 8] }}, {{ ['#818cf8','#f472b6','#fbbf24','#34d399','#60a5fa','#a78bfa','#f87171','#2dd4bf'][$loop->index % 8] }});display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;z-index:2;box-shadow:0 8px 25px {{ ['#6366f1','#ec4899','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ef4444','#14b8a6'][$loop->index % 8] }}40;">
                    <i class="{{ $category->icon ?: 'fas fa-utensils' }}"></i>
                </div>
            </div>
            <div class="card-body" style="text-align:center;">
                <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:.4rem;">{{ $category->name }}</h3>
                @if($category->description)
                <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:.75rem;">{{ Str::limit($category->description, 80) }}</p>
                @endif
                <span class="badge badge-primary" style="font-size:.8rem;">{{ $category->menu_items_count }} {{ Str::plural('item', $category->menu_items_count) }}</span>
                <div style="margin-top:1rem;">
                    <span class="btn btn-outline btn-sm" style="pointer-events:none;">
                        View Menu <i class="fas fa-arrow-right" style="font-size:.7rem;"></i>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:5rem 2rem;">
        <i class="fas fa-utensils" style="font-size:4rem;color:var(--text-dim);margin-bottom:1.5rem;display:block;"></i>
        <h3 style="font-size:1.3rem;margin-bottom:.5rem;">Menu Coming Soon</h3>
        <p style="color:var(--text-muted);">Our food menu is being prepared. Check back soon!</p>
    </div>
    @endif
</div>

@push('styles')
<style>
    .menu-category-card:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,.4) !important;
    }
    .menu-category-card:hover .btn-outline {
        background: var(--primary) !important;
        color: #fff !important;
        border-color: var(--primary) !important;
    }
</style>
@endpush
@endsection
