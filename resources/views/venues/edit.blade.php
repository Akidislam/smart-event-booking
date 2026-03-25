@extends('layouts.app')

@section('title', 'List a Venue - EventVenue')

@section('content')
<div class="page-header pb-4 mb-4 text-center">
    <div class="container-sm">
        <h1>List Your <span class="text-gradient">Venue</span></h1>
        <p>Start earning by hosting events. Fill out the details below to add your venue to the platform.</p>
    </div>
</div>

<div class="container-sm pb-5">
    <div class="card p-4">
        <form action="{{ route('venues.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h3 class="fw-bold mb-3 pb-2 border-bottom" style="border-bottom:1px solid var(--border)">Basic Details</h3>
            
            <div class="form-group">
                <label class="form-label">Venue Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Grand Plaza Convention Hall">
                @error('name')<span class="text-danger fs-sm">{{ $message }}</span>@enderror
            </div>

            <div class="grid-2 gap-3 mb-1">
                <div class="form-group">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-control" required>
                        <option value="">Select category...</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')<span class="text-danger fs-sm">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Maximum Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" required min="1" placeholder="e.g. 500">
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Price per Hour (৳) <span class="text-danger">*</span></label>
                <input type="number" name="price_per_hour" class="form-control" value="{{ old('price_per_hour') }}" required min="0" step="0.01" placeholder="e.g. 5000">
                @error('price_per_hour')<span class="text-danger fs-sm">{{ $message }}</span>@enderror
            </div>

            <div class="form-group mb-5">
                <label class="form-label">Description & Rules</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Describe the space, acoustics, rules, etc.">{{ old('description') }}</textarea>
            </div>

            <h3 class="fw-bold mb-3 pb-2 border-bottom" style="border-bottom:1px solid var(--border)">Location Details</h3>
            
            <div class="form-group">
                <label class="form-label">Full Address <span class="text-danger">*</span></label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}" required placeholder="e.g. 123 Main St, Block B">
            </div>

            <div class="grid-2 gap-3 mb-5">
                <div class="form-group">
                    <label class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}" required placeholder="e.g. Dhaka">
                </div>
                <div class="form-group">
                    <label class="form-label">State/Division</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state') }}" placeholder="e.g. Dhaka Division">
                </div>
            </div>
            
            <input type="hidden" name="latitude" value="{{ old('latitude') }}">
            <input type="hidden" name="longitude" value="{{ old('longitude') }}">

            <h3 class="fw-bold mb-3 pb-2 border-bottom" style="border-bottom:1px solid var(--border)">Media</h3>
            <div class="form-group mb-5">
                <label class="form-label">Venue Images <span class="text-muted text-sm">(Max 5 images)</span></label>
                <div style="border: 2px dashed rgba(99,102,241,0.5); border-radius:var(--radius); padding:2rem; text-align:center; background:rgba(99,102,241,0.05);">
                    <i class="fas fa-cloud-upload-alt" style="font-size:3rem; color:var(--primary-light); margin-bottom:1rem;"></i>
                    <p class="mb-3">Drag and drop images here or click to upload.</p>
                    <input type="file" name="images[]" multiple accept="image/*" class="form-control" style="background:transparent; border:none; padding:0; width:100%;">
                </div>
                <p class="text-muted mt-2 fs-sm"><i class="fas fa-info-circle"></i> High-quality images significantly increase booking rates.</p>
            </div>

            <div class="d-flex justify-between border-top pt-4" style="border-top:1px solid var(--border)">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-check-circle"></i> Publish Venue Listings</button>
            </div>
        </form>
    </div>
</div>
@endsection
