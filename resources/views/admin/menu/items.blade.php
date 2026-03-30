@extends('layouts.app')

@section('title', 'Manage Menu Items — Admin')

@section('content')
<div class="page-header">
    <div class="container">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1><i class="fas fa-bowl-food text-gradient"></i> Menu Items</h1>
                <p>Manage food items and set prices per plate</p>
            </div>
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                <a href="{{ route('admin.menu.categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-layer-group"></i> Categories
                </a>
                <button class="btn btn-primary" onclick="document.getElementById('addItemModal').style.display='flex'">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:4rem;">
    {{-- Filter bar --}}
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-body" style="padding:1rem 1.5rem;">
            <form method="GET" action="{{ route('admin.menu.items.index') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
                <div style="flex:1;min-width:200px;">
                    <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
                </div>
                <div style="min-width:180px;">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filter</button>
                @if(request('search') || request('category'))
                    <a href="{{ route('admin.menu.items.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Clear</a>
                @endif
            </form>
        </div>
    </div>

    @if($items->count())
    <div class="table-wrap">
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Price/Plate</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" style="width:44px;height:44px;border-radius:var(--radius-sm);object-fit:cover;">
                                @else
                                <div style="width:44px;height:44px;border-radius:var(--radius-sm);background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;flex-shrink:0;">
                                    <i class="fas fa-plate-wheat"></i>
                                </div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $item->name }}</div>
                                    @if($item->description)
                                    <div style="color:var(--text-dim);font-size:.75rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-primary">{{ $item->category->name }}</span></td>
                        <td style="font-weight:700;color:var(--success);">৳{{ number_format($item->price_per_plate, 2) }}</td>
                        <td>
                            @if($item->is_available)
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Available</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Unavailable</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:.8rem;">{{ $item->created_at->format('d M Y') }}</td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:.4rem;justify-content:flex-end;">
                                <button class="btn btn-secondary btn-sm" onclick="openEditItem({{ json_encode($item) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.menu.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this menu item?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div style="margin-top:2rem;">{{ $items->appends(request()->query())->links() }}</div>
    @else
    <div style="text-align:center;padding:5rem 2rem;">
        <i class="fas fa-bowl-food" style="font-size:4rem;color:var(--text-dim);margin-bottom:1.5rem;display:block;"></i>
        <h3 style="font-size:1.3rem;margin-bottom:.5rem;">No Menu Items Yet</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">Add your first menu item to get started</p>
        <button class="btn btn-primary" onclick="document.getElementById('addItemModal').style.display='flex'">
            <i class="fas fa-plus"></i> Add Item
        </button>
    </div>
    @endif
</div>

{{-- Add Item Modal --}}
<div id="addItemModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:1rem;">
    <div class="card" style="width:100%;max-width:560px;max-height:90vh;overflow-y:auto;animation:slideIn .3s ease;">
        <div class="card-body" style="padding:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-size:1.25rem;font-weight:700;"><i class="fas fa-plus text-primary"></i> Add Menu Item</h2>
                <button onclick="document.getElementById('addItemModal').style.display='none'" style="background:none;border:none;color:var(--text-muted);font-size:1.2rem;cursor:pointer;"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.menu.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Item Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Fried Rice, Chicken Curry" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price Per Plate (৳) *</label>
                    <input type="number" name="price_per_plate" class="form-control" placeholder="e.g. 500" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Brief description..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                        <input type="checkbox" name="is_available" value="1" checked style="accent-color:var(--primary);width:18px;height:18px;">
                        <span class="form-label" style="margin:0;">Available for ordering</span>
                    </label>
                </div>
                <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('addItemModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Item Modal --}}
<div id="editItemModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:1rem;">
    <div class="card" style="width:100%;max-width:560px;max-height:90vh;overflow-y:auto;animation:slideIn .3s ease;">
        <div class="card-body" style="padding:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-size:1.25rem;font-weight:700;"><i class="fas fa-edit text-primary"></i> Edit Menu Item</h2>
                <button onclick="document.getElementById('editItemModal').style.display='none'" style="background:none;border:none;color:var(--text-muted);font-size:1.2rem;cursor:pointer;"><i class="fas fa-times"></i></button>
            </div>
            <form id="editItemForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select name="category_id" id="editItemCat" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Item Name *</label>
                    <input type="text" name="name" id="editItemName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price Per Plate (৳) *</label>
                    <input type="number" name="price_per_plate" id="editItemPrice" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="editItemDesc" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Image (leave empty to keep current)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                        <input type="checkbox" name="is_available" id="editItemAvail" value="1" style="accent-color:var(--primary);width:18px;height:18px;">
                        <span class="form-label" style="margin:0;">Available for ordering</span>
                    </label>
                </div>
                <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('editItemModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditItem(item) {
    document.getElementById('editItemCat').value = item.category_id;
    document.getElementById('editItemName').value = item.name;
    document.getElementById('editItemPrice').value = item.price_per_plate;
    document.getElementById('editItemDesc').value = item.description || '';
    document.getElementById('editItemAvail').checked = item.is_available;
    document.getElementById('editItemForm').action = '/admin/menu/items/' + item.id;
    document.getElementById('editItemModal').style.display = 'flex';
}
</script>
@endpush
