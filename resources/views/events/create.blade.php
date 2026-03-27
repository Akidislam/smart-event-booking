@extends('layouts.app')

@section('title', 'Create Event - EventVenue')

@section('content')
<div class="page-header pb-4 mb-4 text-center">
    <div class="container mx-auto px-4 max-w-3xl">
        <h1>Create an <span class="text-gradient">Event</span></h1>
        <p>Host an upcoming event, concert, or conference and manage ticketing effortlessly.</p>
    </div>
</div>

<div class="container mx-auto px-4-sm pb-5">
    <div class="card p-4">
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h3 class="fw-bold mb-3 pb-2 border-bottom" style="border-bottom:1px solid var(--border)">Event Details</h3>
            
            <div class="form-group mb-4">
                <label class="form-label">Event Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. Developer Conference 2026">
                @error('title')<span class="text-danger fs-sm">{{ $message }}</span>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 gap-3 mb-4">
                <div class="form-group">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-control" required>
                        <option value="">Select category...</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Venue (Optional)</label>
                    <select name="venue_id" class="form-control">
                        <option value="">No Venue / Online</option>
                        <optgroup label="My Venues">
                            @foreach($venues as $id => $name)
                                <option value="{{ $id }}" {{ old('venue_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="All Other Venues">
                            @foreach($allVenues as $id => $name)
                                @if(!isset($venues[$id]))
                                    <option value="{{ $id }}" {{ old('venue_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <h3 class="fw-bold mb-3 pb-2 border-bottom" style="border-bottom:1px solid var(--border)">Schedule & Ticketing</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 gap-3 mb-4">
                <div class="form-group">
                    <label class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="start_datetime" class="form-control" value="{{ old('start_datetime') }}" required min="{{ date('Y-m-d\TH:i') }}">
                    @error('start_datetime')<span class="text-danger fs-sm">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">End Date & Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="end_datetime" class="form-control" value="{{ old('end_datetime') }}" required min="{{ date('Y-m-d\TH:i') }}">
                    @error('end_datetime')<span class="text-danger fs-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 gap-3 mb-4 align-center">
                <div class="form-group mb-0">
                    <label class="form-label">Max Attendees</label>
                    <input type="number" name="max_attendees" class="form-control" value="{{ old('max_attendees') }}" min="1" placeholder="Leave empty for unlimited">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Ticket Price (৳)</label>
                    <input type="number" name="ticket_price" id="ticket_price" class="form-control" value="{{ old('ticket_price', 0) }}" min="0" step="0.01">
                </div>
                <div class="form-group mb-0 d-flex align-center gap-2 pt-4">
                    <input type="hidden" name="is_free" value="0">
                    <input type="checkbox" name="is_free" id="is_free" value="1" {{ old('is_free') ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary)">
                    <label for="is_free" class="form-label mb-0" style="cursor:pointer;">This is a Free Event</label>
                </div>
            </div>

            <h3 class="fw-bold mb-3 pb-2 border-bottom" style="border-bottom:1px solid var(--border)">Media & Information</h3>

            <div class="form-group mb-4">
                <label class="form-label">Event Banner Image</label>
                <input type="file" name="banner_image" class="form-control" style="background:var(--bg-surface); padding:.5rem;" accept="image/*">
            </div>

            <div class="form-group mb-5">
                <label class="form-label">Event Description & Agenda</label>
                <textarea name="description" class="form-control" rows="6" placeholder="What is this event about? Who should attend?">{{ old('description') }}</textarea>
            </div>

            <div class="d-flex justify-between border-top pt-4" style="border-top:1px solid var(--border)">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary w-full sm:w-auto">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto"><i class="far fa-calendar-check"></i> Publish Event</button>
            </div>
            <div class="text-center w-full mt-3">
                <small class="text-muted"><i class="fab fa-google"></i> This event will automatically be created on your Google Calendar.</small>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('is_free').addEventListener('change', function() {
        const priceInput = document.getElementById('ticket_price');
        if (this.checked) {
            priceInput.value = 0;
            priceInput.readOnly = true;
            priceInput.style.opacity = '0.5';
        } else {
            priceInput.readOnly = false;
            priceInput.style.opacity = '1';
        }
    });
</script>
@endpush
@endsection
