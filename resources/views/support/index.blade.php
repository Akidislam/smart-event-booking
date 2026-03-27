@extends('layouts.app')

@section('title', 'Support — EventVenue')
@section('meta_description', 'Contact our support team and view your message history.')

@push('styles')
<style>
/* ── Support Page Layout ── */
.support-wrap {
    max-width: 820px;
    margin: 0 auto;
    padding: 0 1.5rem 5rem;
}

/* ── Compose box ── */
.compose-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem;
    margin-bottom: 2.5rem;
}
.compose-card h2 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.35rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: .6rem;
}
.compose-card h2 i { color: var(--primary-light); }

/* ── Thread ── */
.thread-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}
.thread-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
}

.message-bubble-wrap {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    margin-bottom: 2rem;
}

/* User message — left */
.msg-user {
    display: flex;
    gap: .85rem;
    align-items: flex-start;
}
.msg-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    flex-shrink: 0;
    object-fit: cover;
    border: 2px solid var(--primary);
}
.msg-avatar-icon {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .9rem; flex-shrink: 0;
}
.msg-bubble {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 0 var(--radius) var(--radius) var(--radius);
    padding: 1rem 1.25rem;
    flex: 1;
}
.msg-bubble-meta {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: .5rem;
    flex-wrap: wrap;
}
.msg-sender { font-weight: 600; font-size: .875rem; }
.msg-time { font-size: .78rem; color: var(--text-dim); }
.msg-text { font-size: .9rem; line-height: 1.65; color: var(--text); }

/* Admin reply — right */
.msg-admin {
    display: flex;
    gap: .85rem;
    align-items: flex-start;
    flex-direction: row-reverse;
}
.msg-admin .msg-bubble {
    background: rgba(99,102,241,0.1);
    border-color: rgba(99,102,241,0.25);
    border-radius: var(--radius) 0 var(--radius) var(--radius);
}
.msg-admin .msg-avatar-icon {
    background: linear-gradient(135deg, #10b981, #059669);
}

/* Status badge */
.status-pending  { background:rgba(245,158,11,0.15); color:#fbbf24; }
.status-replied  { background:rgba(16,185,129,0.15); color:#34d399; }

/* Empty state */
.empty-thread {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
}
.empty-thread i { font-size: 3.5rem; display: block; margin-bottom: 1rem; opacity: .35; }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="page-header pb-4 mb-0 text-center">
    <div class="container mx-auto px-4 max-w-3xl">
        <h1><i class="fas fa-headset" style="color:var(--primary-light);"></i> Support <span class="text-gradient">Center</span></h1>
        <p>Need help? Send us a message and our team will get back to you promptly.</p>
    </div>
</div>

<div class="container mx-auto px-4">
    <div class="support-wrap">

        <!-- Compose Card -->
        <div class="compose-card">
            <h2><i class="fas fa-paper-plane"></i> Send a Message</h2>
            <form action="{{ route('support.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Your Message <span class="text-danger">*</span></label>
                    <textarea
                        name="message"
                        class="form-control"
                        rows="5"
                        placeholder="Describe your issue or question in detail…"
                        required
                        minlength="10">{{ old('message') }}</textarea>
                    @error('message')
                        <span class="text-danger fs-sm"><i class="fas fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full sm:w-auto">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>

        <!-- Message Thread -->
        <div class="thread-header">
            <h3><i class="fas fa-comments"></i> Your Messages</h3>
            <span class="badge badge-primary">{{ $messages->count() }} total</span>
        </div>

        @forelse($messages as $msg)
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem;">

            <!-- Status + date header -->
            <div class="d-flex justify-between align-center mb-2" style="flex-wrap:wrap;gap:.5rem;">
                <span style="font-size:.78rem;color:var(--text-dim);">
                    <i class="fas fa-clock"></i> {{ $msg->created_at->format('d M Y, h:i A') }}
                </span>
                <span class="badge status-{{ $msg->status }}">
                    @if($msg->status === 'pending')
                        <i class="fas fa-hourglass-half"></i> Pending
                    @else
                        <i class="fas fa-check-circle"></i> Replied
                    @endif
                </span>
            </div>

            <div class="message-bubble-wrap">
                <!-- User message (left) -->
                <div class="msg-user">
                    <img src="{{ auth()->user()->avatar_url }}" alt="You" class="msg-avatar w-full h-auto object-cover w-full h-auto object-cover">
                    <div class="msg-bubble">
                        <div class="msg-bubble-meta">
                            <span class="msg-sender">You</span>
                        </div>
                        <div class="msg-text">{{ $msg->message }}</div>
                    </div>
                </div>

                <!-- Admin reply (right) -->
                @if($msg->reply)
                <div class="msg-admin">
                    <div class="msg-avatar-icon"><i class="fas fa-shield-halved"></i></div>
                    <div class="msg-bubble">
                        <div class="msg-bubble-meta">
                            <span class="msg-sender" style="color:var(--primary-light);">Support Team</span>
                        </div>
                        <div class="msg-text">{{ $msg->reply }}</div>
                    </div>
                </div>
                @else
                <div style="text-align:center;font-size:.82rem;color:var(--text-dim);padding:.5rem 0;">
                    <i class="fas fa-hourglass-half"></i> Awaiting admin reply…
                </div>
                @endif
            </div>

        </div>
        @empty
        <div class="empty-thread">
            <i class="fas fa-inbox"></i>
            <p style="font-size:1.05rem;margin-bottom:.5rem;">No messages yet</p>
            <p class="fs-sm">Use the form above to send your first message.</p>
        </div>
        @endforelse

    </div>
</div>
@endsection
