@extends('layouts.app')

@section('title', 'Manage Menu Categories — Admin')

@section('content')
<div class="page-header">
    <div class="container">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1><i class="fas fa-layer-group text-gradient"></i> Menu Categories</h1>
                <p>Create and manage food menu categories for your events</p>
            </div>
            <button class="btn btn-primary" onclick="document.getElementById('addCategoryModal').style.display='flex'">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>
    </div>
</div>

<div class="container" style="padding-bottom:4rem;">
    @if($categories->count())
    <div class="grid-3">
        @foreach($categories as $category)
        <div class="card" style="position:relative;">
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                    <div style="width:52px;height:52px;border-radius:var(--radius-sm);background:linear-gradient(135deg, var(--primary), var(--secondary));display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff;flex-shrink:0;">
                        <i class="{{ $category->icon ?: 'fas fa-utensils' }}"></i>
                    </div>
                    <div>
                        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.15rem;">{{ $category->name }}</h3>
                        <span class="badge badge-primary">{{ $category->menu_items_count }} items</span>
                    </div>
                </div>
                @if($category->description)
                <p style="color:var(--text-muted);font-size:.875rem;margin-bottom:1rem;line-height:1.6;">{{ $category->description }}</p>
                @endif
                <div style="display:flex;gap:.5rem;margin-top:1rem;">
                    <button class="btn btn-secondary btn-sm" onclick="openEditCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->icon) }}', '{{ addslashes($category->description) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('admin.menu.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category and all its items?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:2rem;">{{ $categories->links() }}</div>
    @else
    <div style="text-align:center;padding:5rem 2rem;">
        <i class="fas fa-utensils" style="font-size:4rem;color:var(--text-dim);margin-bottom:1.5rem;display:block;"></i>
        <h3 style="font-size:1.3rem;margin-bottom:.5rem;">No Categories Yet</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">Start by adding your first menu category</p>
        <button class="btn btn-primary" onclick="document.getElementById('addCategoryModal').style.display='flex'">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>
    @endif
</div>

{{-- Add Category Modal --}}
<div id="addCategoryModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:1rem;">
    <div class="card" style="width:100%;max-width:500px;animation:slideIn .3s ease;">
        <div class="card-body" style="padding:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-size:1.25rem;font-weight:700;"><i class="fas fa-plus text-primary"></i> Add Category</h2>
                <button onclick="document.getElementById('addCategoryModal').style.display='none'" style="background:none;border:none;color:var(--text-muted);font-size:1.2rem;cursor:pointer;"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.menu.categories.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Chinese, Deshi, Continental" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Icon Class (FontAwesome)</label>
                    <input type="text" name="icon" class="form-control" placeholder="e.g. fas fa-bowl-rice">
                    <small style="color:var(--text-dim);font-size:.75rem;margin-top:.3rem;display:block;">Leave empty for default utensils icon</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this category..."></textarea>
                </div>
                <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('addCategoryModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div id="editCategoryModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:1rem;">
    <div class="card" style="width:100%;max-width:500px;animation:slideIn .3s ease;">
        <div class="card-body" style="padding:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h2 style="font-size:1.25rem;font-weight:700;"><i class="fas fa-edit text-primary"></i> Edit Category</h2>
                <button onclick="document.getElementById('editCategoryModal').style.display='none'" style="background:none;border:none;color:var(--text-muted);font-size:1.2rem;cursor:pointer;"><i class="fas fa-times"></i></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" id="editCatName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Icon Class (FontAwesome)</label>
                    <input type="text" name="icon" id="editCatIcon" class="form-control" placeholder="e.g. fas fa-bowl-rice">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="editCatDesc" class="form-control" rows="3"></textarea>
                </div>
                <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('editCategoryModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditCategory(id, name, icon, description) {
    document.getElementById('editCatName').value = name;
    document.getElementById('editCatIcon').value = icon;
    document.getElementById('editCatDesc').value = description;
    document.getElementById('editCategoryForm').action = '/admin/menu/categories/' + id;
    document.getElementById('editCategoryModal').style.display = 'flex';
}
</script>
@endpush
