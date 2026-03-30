@extends('layouts.app')

@section('title', $category->name . ' Menu — EventVenue')
@section('meta_description', 'Browse ' . $category->name . ' food items for your event. Select dishes and calculate your total bill instantly.')

@section('content')
<div class="page-header">
    <div class="container">
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
            <a href="{{ route('menu.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> All Categories
            </a>
            <div>
                <h1><i class="{{ $category->icon ?: 'fas fa-utensils' }} text-gradient"></i> {{ $category->name }} Menu</h1>
                <p>Select dishes and set guest count to calculate your total bill</p>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:5rem;">
    {{-- Category Tabs --}}
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:2rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
        @foreach($categories as $cat)
        <a href="{{ route('menu.show', $cat) }}"
           class="btn {{ $cat->id === $category->id ? 'btn-primary' : 'btn-secondary' }} btn-sm"
           style="border-radius:50px;">
            <i class="{{ $cat->icon ?: 'fas fa-utensils' }}"></i> {{ $cat->name }}
        </a>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:start;">
        {{-- Menu Items Grid --}}
        <div>
            @if($items->count())
            <div class="menu-items-grid">
                @foreach($items as $item)
                <div class="card menu-item-card" id="item-card-{{ $item->id }}">
                    @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="card-img">
                    @else
                    <div style="height:160px;background:linear-gradient(135deg, var(--bg-surface), var(--bg-card-hover));display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plate-wheat" style="font-size:3rem;color:var(--text-dim);"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem;margin-bottom:.5rem;">
                            <h3 style="font-size:1rem;font-weight:700;">{{ $item->name }}</h3>
                            <span style="font-weight:800;color:var(--success);white-space:nowrap;font-size:1.05rem;">৳{{ number_format($item->price_per_plate, 0) }}</span>
                        </div>
                        @if($item->description)
                        <p style="color:var(--text-muted);font-size:.8rem;line-height:1.5;margin-bottom:.75rem;">{{ $item->description }}</p>
                        @endif
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--border);">
                            <span style="color:var(--text-dim);font-size:.75rem;">per plate</span>
                            <label class="menu-checkbox-label">
                                <input type="checkbox"
                                       class="menu-item-checkbox"
                                       data-price="{{ $item->price_per_plate }}"
                                       data-name="{{ $item->name }}"
                                       data-id="{{ $item->id }}"
                                       onchange="calculateBill()">
                                <span class="menu-checkbox-btn">
                                    <i class="fas fa-plus"></i> Select
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align:center;padding:4rem 2rem;">
                <i class="fas fa-plate-wheat" style="font-size:3.5rem;color:var(--text-dim);margin-bottom:1.5rem;display:block;"></i>
                <h3 style="font-size:1.25rem;margin-bottom:.5rem;">No Items Available</h3>
                <p style="color:var(--text-muted);">This category doesn't have any menu items yet.</p>
            </div>
            @endif
        </div>

        {{-- Sticky Billing Sidebar --}}
        <div class="billing-sidebar">
            <div class="card" style="position:sticky;top:90px;">
                <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);background:linear-gradient(135deg, rgba(99,102,241,0.08), rgba(236,72,153,0.05));">
                    <h3 style="font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                        <i class="fas fa-calculator text-primary"></i> Bill Calculator
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Guest Input --}}
                    <div class="form-group">
                        <label class="form-label" style="font-weight:600;">
                            <i class="fas fa-users" style="color:var(--primary-light);"></i> Number of Guests
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-user-group"></i>
                            <input type="number" id="guestCount" class="form-control"
                                   placeholder="Enter guest count"
                                   min="1" value="1"
                                   oninput="calculateBill()">
                        </div>
                        <div id="guestError" style="display:none;color:var(--danger);font-size:.75rem;margin-top:.4rem;">
                            <i class="fas fa-exclamation-circle"></i> Guest count must be at least 1
                        </div>
                    </div>

                    {{-- Selected Items List --}}
                    <div id="selectedItemsList" style="margin-bottom:1.25rem;">
                        <p style="color:var(--text-dim);font-size:.85rem;text-align:center;padding:1.5rem 0;">
                            <i class="fas fa-hand-pointer" style="display:block;font-size:1.5rem;margin-bottom:.5rem;"></i>
                            Select items from the menu
                        </p>
                    </div>

                    {{-- Bill Summary --}}
                    <div id="billSummary" style="display:none;">
                        <div style="border-top:1px solid var(--border);padding-top:1rem;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.85rem;">
                                <span style="color:var(--text-muted);">Items Selected:</span>
                                <span id="itemCount" style="font-weight:600;">0</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.85rem;">
                                <span style="color:var(--text-muted);">Cost Per Guest:</span>
                                <span id="perGuestCost" style="font-weight:600;">৳0</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.85rem;">
                                <span style="color:var(--text-muted);">Guests:</span>
                                <span id="guestDisplay" style="font-weight:600;">0</span>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div style="background:linear-gradient(135deg, var(--primary), var(--primary-dark));border-radius:var(--radius-sm);padding:1.25rem;margin-top:1rem;text-align:center;">
                            <div style="font-size:.75rem;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.08em;font-weight:600;margin-bottom:.25rem;">Total Bill</div>
                            <div id="totalBill" style="font-size:2rem;font-weight:900;color:#fff;">৳0</div>
                        </div>
                    </div>

                    {{-- Empty Total --}}
                    <div id="emptyTotal" style="background:var(--bg-surface);border-radius:var(--radius-sm);padding:1.25rem;text-align:center;border:1px dashed var(--border);">
                        <div style="font-size:.75rem;color:var(--text-dim);text-transform:uppercase;letter-spacing:.08em;font-weight:600;margin-bottom:.25rem;">Total Bill</div>
                        <div style="font-size:2rem;font-weight:900;color:var(--text-dim);">৳0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .menu-items-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }

    .menu-item-card.selected {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.3), 0 8px 25px rgba(99,102,241,0.15) !important;
    }

    .menu-checkbox-label {
        cursor: pointer;
        display: inline-block;
    }
    .menu-checkbox-label input[type="checkbox"] {
        display: none;
    }
    .menu-checkbox-btn {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .9rem;
        border-radius: 50px;
        font-size: .8rem;
        font-weight: 600;
        background: var(--glass);
        border: 1px solid var(--border-strong);
        color: var(--text-muted);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .menu-checkbox-label input:checked + .menu-checkbox-btn {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-color: var(--primary);
        color: #fff;
        box-shadow: 0 4px 12px rgba(99,102,241,0.35);
    }
    .menu-checkbox-label input:checked + .menu-checkbox-btn i::before {
        content: "\f00c"; /* fa-check */
    }

    .selected-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .6rem .75rem;
        border-radius: var(--radius-sm);
        background: var(--glass);
        margin-bottom: .4rem;
        font-size: .85rem;
        animation: fadeInUp .3s ease;
    }
    .selected-item-row .item-name {
        font-weight: 500;
        color: var(--text);
        flex: 1;
        margin-right: .5rem;
    }
    .selected-item-row .item-price {
        font-weight: 700;
        color: var(--success);
        white-space: nowrap;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .billing-sidebar {
        min-width: 0;
    }

    @media (max-width: 1024px) {
        .container > div[style*="grid-template-columns: 1fr 360px"] {
            grid-template-columns: 1fr !important;
        }
        .billing-sidebar .card {
            position: fixed !important;
            bottom: 0;
            left: 0;
            right: 0;
            top: auto !important;
            z-index: 999;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            box-shadow: 0 -10px 40px rgba(0,0,0,.5);
            max-height: 40vh;
            overflow-y: auto;
            transform: translateY(calc(100% - 80px));
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .billing-sidebar .card.expanded {
            transform: translateY(0);
        }
    }
    @media (max-width: 768px) {
        .menu-items-grid {
            grid-template-columns: 1fr !important;
        }
    }
    @media (min-width: 769px) and (max-width: 1024px) {
        .menu-items-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@push('scripts')
<script>
function calculateBill() {
    const guestInput = document.getElementById('guestCount');
    const guestError = document.getElementById('guestError');
    const selectedList = document.getElementById('selectedItemsList');
    const billSummary = document.getElementById('billSummary');
    const emptyTotal = document.getElementById('emptyTotal');

    let guestCount = parseInt(guestInput.value) || 0;

    // Validate guest count
    if (guestCount < 1) {
        guestError.style.display = 'block';
        guestInput.style.borderColor = 'var(--danger)';
    } else {
        guestError.style.display = 'none';
        guestInput.style.borderColor = '';
    }

    // Ensure minimum 1 for calculation
    if (guestCount < 1) guestCount = 0;

    const checkboxes = document.querySelectorAll('.menu-item-checkbox');
    let selectedItems = [];
    let totalPerGuest = 0;

    checkboxes.forEach(cb => {
        const card = document.getElementById('item-card-' + cb.dataset.id);
        if (cb.checked) {
            card.classList.add('selected');
            selectedItems.push({
                name: cb.dataset.name,
                price: parseFloat(cb.dataset.price)
            });
            totalPerGuest += parseFloat(cb.dataset.price);
        } else {
            card.classList.remove('selected');
        }
    });

    // Update selected items list
    if (selectedItems.length > 0) {
        let html = '';
        selectedItems.forEach(item => {
            html += `<div class="selected-item-row">
                <span class="item-name"><i class="fas fa-check-circle text-success" style="font-size:.7rem;margin-right:.3rem;"></i>${item.name}</span>
                <span class="item-price">৳${item.price.toLocaleString()}</span>
            </div>`;
        });
        selectedList.innerHTML = html;
        billSummary.style.display = 'block';
        emptyTotal.style.display = 'none';

        const total = totalPerGuest * guestCount;
        document.getElementById('itemCount').textContent = selectedItems.length;
        document.getElementById('perGuestCost').textContent = '৳' + totalPerGuest.toLocaleString();
        document.getElementById('guestDisplay').textContent = guestCount;
        document.getElementById('totalBill').textContent = '৳' + total.toLocaleString();

        // Add pulse animation to total
        const totalEl = document.getElementById('totalBill');
        totalEl.style.transform = 'scale(1.1)';
        setTimeout(() => { totalEl.style.transform = 'scale(1)'; }, 200);
    } else {
        selectedList.innerHTML = `<p style="color:var(--text-dim);font-size:.85rem;text-align:center;padding:1.5rem 0;">
            <i class="fas fa-hand-pointer" style="display:block;font-size:1.5rem;margin-bottom:.5rem;"></i>
            Select items from the menu
        </p>`;
        billSummary.style.display = 'none';
        emptyTotal.style.display = 'block';
    }

    // Expand mobile billing drawer if items selected
    const sidebar = document.querySelector('.billing-sidebar .card');
    if (sidebar && window.innerWidth <= 1024) {
        if (selectedItems.length > 0) {
            sidebar.classList.add('expanded');
        }
    }
}

// Mobile billing drawer toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.billing-sidebar .card');
    if (sidebar) {
        const header = sidebar.querySelector('div[style*="border-bottom"]');
        if (header) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    sidebar.classList.toggle('expanded');
                }
            });
        }
    }
});

// Transition for total bill
document.addEventListener('DOMContentLoaded', function() {
    const totalEl = document.getElementById('totalBill');
    if (totalEl) {
        totalEl.style.transition = 'transform 0.2s ease';
    }
});
</script>
@endpush
@endsection
