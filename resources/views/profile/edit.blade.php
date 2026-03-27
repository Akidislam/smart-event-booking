@extends('layouts.app')

@section('title', 'Edit Profile — EventVenue')

@push('styles')
<style>
.edit-wrap {
    max-width: 680px;
    margin: 0 auto;
    padding: 0 1.5rem 5rem;
}
.edit-section-title {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-dim);
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid var(--border);
}
.avatar-preview-wrap {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
}
.avatar-preview {
    width: 80px; height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary);
    flex-shrink: 0;
}
.avatar-info { flex: 1; }
.avatar-info p { font-size: .85rem; color: var(--text-muted); line-height: 1.5; }
.avatar-info small { font-size: .75rem; color: var(--text-dim); }
.form-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem;
    margin-bottom: 1.5rem;
}
.char-count {
    float: right;
    font-size: .75rem;
    color: var(--text-dim);
}
</style>
@endpush

@section('content')

<div class="page-header pb-4 mb-0" style="padding-top:2.5rem;">
    <div class="container">
        <div class="d-flex align-center gap-2">
            <a href="{{ route('profile.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div>
                <h1 style="font-size:1.85rem;">Edit <span class="text-gradient">Profile</span></h1>
                <p class="text-muted" style="margin-top:.25rem;">Update your personal information.</p>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-top:2rem;">
    <div class="edit-wrap">

        {{-- Avatar Preview --}}
        <div class="avatar-preview-wrap">
            <img class="avatar-preview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatarPreview">
            <div class="avatar-info">
                <div style="font-weight:600;margin-bottom:.3rem;">{{ $user->name }}</div>
                @if($user->google_id)
                    <p><i class="fab fa-google" style="color:#ea4335;"></i> Your avatar is synced from Google. To change it, update your Google profile photo.</p>
                @else
                    <p>Your avatar is auto-generated from your name using UI Avatars.</p>
                @endif
                <small>{{ $user->email }}</small>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
            @csrf

            {{-- Personal Information --}}
            <div class="form-card">
                <div class="edit-section-title"><i class="fas fa-user"></i> Personal Information</div>

                <div class="form-group">
                    <label class="form-label">
                        Full Name <span class="text-danger">*</span>
                        <span class="char-count" id="nameCount">0 / 255</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="nameInput"
                        class="form-control"
                        value="{{ old('name', $user->name) }}"
                        required
                        maxlength="255"
                        placeholder="Your full name"
                        autocomplete="name"
                    >
                    @error('name')
                        <span class="text-danger fs-sm mt-1" style="display:block;">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $user->email) }}"
                        required
                        maxlength="255"
                        placeholder="your@email.com"
                        autocomplete="email"
                        @if($user->google_id) readonly style="opacity:.6;cursor:not-allowed;" @endif
                    >
                    @if($user->google_id)
                        <small class="text-muted fs-sm" style="display:block;margin-top:.35rem;">
                            <i class="fas fa-info-circle"></i> Email is managed by Google and cannot be changed here.
                        </small>
                    @endif
                    @error('email')
                        <span class="text-danger fs-sm mt-1" style="display:block;">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">Phone Number <span class="text-muted fs-sm">(optional)</span></label>
                    <div class="input-icon">
                        <i class="fas fa-phone"></i>
                        <input
                            type="tel"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $user->phone) }}"
                            maxlength="20"
                            placeholder="+880 1XXX-XXXXXX"
                            autocomplete="tel"
                        >
                    </div>
                    @error('phone')
                        <span class="text-danger fs-sm mt-1" style="display:block;">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Account Info (read-only) --}}
            <div class="form-card">
                <div class="edit-section-title"><i class="fas fa-shield-halved"></i> Account Information</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <div class="form-label">Account Role</div>
                        <div style="padding:.65rem 1rem;background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:.9rem;color:var(--text-muted);">
                            {{ ucfirst(str_replace('_', ' ', $user->role ?? 'user')) }}
                        </div>
                    </div>
                    <div>
                        <div class="form-label">Member Since</div>
                        <div style="padding:.65rem 1rem;background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:.9rem;color:var(--text-muted);">
                            {{ $user->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2" style="justify-content:flex-end;flex-wrap:wrap;">
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                    <i class="fas fa-xmark"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-lg" id="saveBtn">
                    <i class="fas fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
// Character counter for name
const nameInput = document.getElementById('nameInput');
const nameCount = document.getElementById('nameCount');
function updateCount() {
    nameCount.textContent = nameInput.value.length + ' / 255';
}
nameInput.addEventListener('input', updateCount);
updateCount();

// Prevent double-submit
document.getElementById('profileForm').addEventListener('submit', function() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';
});
</script>
@endpush
