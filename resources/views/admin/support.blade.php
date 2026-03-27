@extends('layouts.app')

@section('title', 'Support Messages — Admin | EventVenue')

@push('styles')
<style>
.support-msg-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    transition: var(--transition);
}
.support-msg-card:hover {
    border-color: var(--border-strong);
}
.support-msg-card.pending {
    border-left: 3px solid var(--warning);
}
.support-msg-card.replied {
    border-left: 3px solid var(--success);
}
.user-info {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: 1rem;
}
.user-info img {
    width: 42px; height: 42px;
    border-radius: 50%;
    border: 2px solid var(--border-strong);
    object-fit: cover;
}
.user-name { font-weight: 600; font-size: .9rem; }
.user-email { font-size: .78rem; color: var(--text-muted); }
.msg-body {
    background: var(--bg-surface);
    border-radius: var(--radius-sm);
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    font-size: .9rem;
    line-height: 1.65;
    color: var(--text);
    border: 1px solid var(--border);
}
.reply-bubble {
    background: rgba(16,185,129,0.08);
    border: 1px solid rgba(16,185,129,0.25);
    border-radius: var(--radius-sm);
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    font-size: .9rem;
    line-height: 1.65;
    color: #a7f3d0;
}
.reply-form textarea { min-height: 90px; }
.filter-bar {
    display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    margin-bottom: 2rem;
    padding: 1rem 1.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
}
.status-pending { background:rgba(245,158,11,0.15); color:#fbbf24; }
.status-replied  { background:rgba(16,185,129,0.15); color:#34d399; }
</style>
@endpush

@section('content')
<div class="page-header pb-4 mb-0">
    <div class="container mx-auto px-4">
        <div class="d-flex justify-between align-center" style="flex-wrap:wrap;gap:1rem;">
            <div>
                <h1><i class="fas fa-headset" style="color:var(--primary-light);font-size:2rem;"></i>
                    Support <span class="text-gradient">Messages</span>
                </h1>
                <p class="text-muted mt-1">Respond to user support requests</p>
            </div>
            @if($pendingCount > 0)
                <span class="badge badge-warning" style="font-size:.9rem;padding:.5rem 1.2rem;">
                    <i class="fas fa-hourglass-half"></i> {{ $pendingCount }} Pending
                </span>
            @else
                <span class="badge badge-success" style="font-size:.9rem;padding:.5rem 1.2rem;">
                    <i class="fas fa-check-circle"></i> All replied
                </span>
            @endif
        </div>
    </div>
</div>

<div class="container mx-auto px-4" style="padding-top:2rem;padding-bottom:4rem;">

    <!-- Filter bar -->
    <form method="GET" class="filter-bar">
        <select name="status" class="form-control" style="width:auto;">
            <option value="">All Messages</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="replied"  {{ request('status') === 'replied'  ? 'selected' : '' }}>Replied</option>
        </select>
        <button type="submit" class="btn btn-secondary w-full sm:w-auto"><i class="fas fa-filter"></i> Filter</button>
        @if(request('status'))
            <a href="{{ route('admin.support.index') }}" class="btn btn-secondary w-full sm:w-auto">
                <i class="fas fa-xmark"></i> Clear
            </a>
        @endif
        <span style="margin-left:auto;color:var(--text-muted);font-size:.85rem;">
            {{ $messages->total() }} message(s) found
        </span>
    </form>

    @forelse($messages as $msg)
    <div class="support-msg-card {{ $msg->status }}">

        <!-- User info row -->
        <div class="user-info">
            <img class="w-full h-auto object-cover w-full h-auto object-cover" src="{{ $msg->user->avatar_url }}" alt="{{ $msg->user->name }}">
            <div>
                <div class="user-name">{{ $msg->user->name }}</div>
                <div class="user-email">{{ $msg->user->email }}</div>
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
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
        </div>

        <!-- User's message -->
        <p style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-dim);margin-bottom:.4rem;font-weight:600;">
            <i class="fas fa-user"></i> User's Message
        </p>
        <div class="msg-body">{{ $msg->message }}</div>

        <!-- Existing reply -->
        @if($msg->reply)
        <p style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6ee7b7;margin-bottom:.4rem;font-weight:600;">
            <i class="fas fa-shield-halved"></i> Your Reply
        </p>
        <div class="reply-bubble">{{ $msg->reply }}</div>
        @endif

        <!-- Reply form -->
        <form action="{{ route('admin.support.reply', $msg) }}" method="POST" class="reply-form">
            @csrf
            <div class="form-group" style="margin-bottom:.75rem;">
                <label class="form-label">
                    {{ $msg->reply ? 'Update Reply' : 'Write a Reply' }}
                    <span class="text-danger">*</span>
                </label>
                <textarea
                    name="reply"
                    class="form-control"
                    placeholder="Type your reply here…"
                    required>{{ old('reply', $msg->reply) }}</textarea>
            </div>
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-reply"></i> {{ $msg->reply ? 'Update Reply' : 'Send Reply' }}
            </button>
        </form>

    </div>
    @empty
    <div style="text-align:center;padding:4rem;color:var(--text-muted);">
        <i class="fas fa-inbox" style="font-size:4rem;opacity:.3;display:block;margin-bottom:1rem;"></i>
        <p style="font-size:1.1rem;">No support messages found.</p>
    </div>
    @endforelse

    <div>{{ $messages->links() }}</div>

</div>
@endsection
