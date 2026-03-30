@extends('layouts.app')

@section('title', 'Live Chat — EventVenue')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════
   WHATSAPP / MESSENGER STYLE CHAT
   ═══════════════════════════════════════════════════ */

/* ─── Chat Shell ─── */
.chat-shell {
    height: calc(100vh - var(--nav-h) - 1px);
    display: flex;
    overflow: hidden;
    background: var(--bg);
}

/* ─── Sidebar ─── */
.chat-sidebar {
    width: 340px;
    min-width: 300px;
    background: var(--bg-surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}

.sidebar-header {
    padding: 1rem 1.15rem;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.sidebar-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: .95rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .6rem;
}
.sidebar-title i {
    color: var(--primary-light);
    font-size: .85rem;
}

.sidebar-search {
    width: 100%;
    padding: .5rem .85rem .5rem 2.2rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    font-size: .82rem;
    font-family: inherit;
    transition: var(--transition);
}
.sidebar-search:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.sidebar-search::placeholder { color: var(--text-dim); }
.search-wrap {
    position: relative;
}
.search-wrap i {
    position: absolute;
    left: .75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-dim);
    font-size: .75rem;
}

/* ─── Contact List ─── */
.contacts-list {
    flex: 1;
    overflow-y: auto;
    padding: .35rem;
}
.contacts-list::-webkit-scrollbar { width: 3px; }
.contacts-list::-webkit-scrollbar-track { background: transparent; }
.contacts-list::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 3px; }

.contact-item {
    display: flex;
    align-items: center;
    gap: .7rem;
    padding: .6rem .8rem;
    border-radius: 10px;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    position: relative;
    margin-bottom: 1px;
}
.contact-item:hover {
    background: var(--glass-hover);
}
.contact-item.active {
    background: rgba(99,102,241,0.12);
}

.contact-avatar {
    position: relative;
    flex-shrink: 0;
}
.contact-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border);
}
.contact-item.active .contact-avatar img {
    border-color: var(--primary);
}
.contact-online {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    background: var(--success);
    border-radius: 50%;
    border: 2px solid var(--bg-surface);
}
.contact-unread {
    position: absolute;
    top: -2px;
    right: -4px;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    background: var(--secondary);
    border-radius: 50px;
    font-size: .6rem;
    font-weight: 700;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--bg-surface);
}

.contact-info {
    flex: 1;
    min-width: 0;
}
.contact-name {
    font-size: .85rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--text);
}
.contact-preview {
    font-size: .75rem;
    color: var(--text-dim);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: .1rem;
}
.contact-time {
    font-size: .65rem;
    color: var(--text-dim);
    white-space: nowrap;
    flex-shrink: 0;
    align-self: flex-start;
    margin-top: .15rem;
}
.contact-item.active .contact-name { color: var(--primary-light); }

/* ─── Chat Main Area ─── */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
    background: var(--bg);
}

/* ─── Chat Header ─── */
.chat-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem 1.25rem;
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.chat-header-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid var(--primary);
    object-fit: cover;
    flex-shrink: 0;
}
.chat-header-info {
    flex: 1;
    min-width: 0;
}
.chat-header-name {
    font-weight: 700;
    font-size: .9rem;
    line-height: 1.2;
}
.chat-header-status {
    font-size: .72rem;
    color: var(--success);
    display: flex;
    align-items: center;
    gap: .3rem;
}
.chat-header-status .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--success);
    display: inline-block;
}
.chat-header-actions {
    display: flex;
    gap: .4rem;
}
.chat-header-actions button {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--glass);
    border: 1px solid var(--border);
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    transition: var(--transition);
}
.chat-header-actions button:hover {
    background: var(--glass-hover);
    color: var(--text);
}

/* ─── Messages Area ─── */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: .35rem;
    scroll-behavior: smooth;
    background:
        radial-gradient(ellipse 80% 60% at 20% 80%, rgba(99,102,241,0.04) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 85% 20%, rgba(236,72,153,0.03) 0%, transparent 60%);
}
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

/* ─── Message Bubbles ─── */
.msg-row {
    display: flex;
    gap: .4rem;
    max-width: 65%;
    animation: msgSlide .25s ease;
}
@keyframes msgSlide {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.msg-row.mine {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.msg-row.theirs {
    align-self: flex-start;
}

.msg-bubble {
    padding: .5rem .85rem;
    border-radius: 16px;
    font-size: .875rem;
    line-height: 1.5;
    word-break: break-word;
    position: relative;
}
.msg-row.mine .msg-bubble {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.msg-row.theirs .msg-bubble {
    background: var(--bg-card);
    color: var(--text);
    border: 1px solid var(--border);
    border-bottom-left-radius: 4px;
}

.msg-time {
    font-size: .62rem;
    color: var(--text-dim);
    margin-top: .15rem;
    padding: 0 .4rem;
}
.msg-row.mine .msg-time {
    text-align: right;
    color: rgba(255,255,255,.45);
}
.msg-row.mine .msg-time { color: var(--text-dim); }

/* ─── Message Attachment ─── */
.msg-image {
    max-width: 260px;
    max-height: 220px;
    border-radius: 12px;
    object-fit: cover;
    cursor: pointer;
    display: block;
    margin-bottom: .3rem;
    transition: transform .2s;
}
.msg-image:hover { transform: scale(1.02); }

.msg-file {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .5rem .75rem;
    background: rgba(255,255,255,.06);
    border-radius: 10px;
    margin-bottom: .3rem;
    text-decoration: none;
    color: inherit;
    transition: background .2s;
    min-width: 160px;
}
.msg-file:hover { background: rgba(255,255,255,.1); }
.msg-file-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: rgba(99,102,241,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-light);
    font-size: .85rem;
    flex-shrink: 0;
}
.msg-row.mine .msg-file-icon {
    background: rgba(255,255,255,.15);
    color: #fff;
}
.msg-file-info {
    flex: 1;
    min-width: 0;
}
.msg-file-name {
    font-size: .78rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.msg-file-size {
    font-size: .65rem;
    opacity: .6;
}

/* ─── Date Separator ─── */
.date-sep {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin: .75rem 0;
    align-self: center;
}
.date-sep span {
    font-size: .68rem;
    color: var(--text-dim);
    white-space: nowrap;
    font-weight: 600;
    background: var(--bg-surface);
    padding: .25rem .75rem;
    border-radius: 50px;
    border: 1px solid var(--border);
}

/* ─── Empty State ─── */
.chat-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    gap: .75rem;
    padding: 2rem;
    text-align: center;
}
.chat-empty-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(99,102,241,0.06);
    border: 2px dashed rgba(99,102,241,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: var(--primary-light);
    margin-bottom: .25rem;
}
.chat-empty p {
    font-size: .9rem;
    max-width: 320px;
    line-height: 1.6;
    color: var(--text-dim);
}

/* ─── Input Bar (WhatsApp-style) ─── */
.chat-input-bar {
    display: flex;
    align-items: flex-end;
    gap: .5rem;
    padding: .6rem 1rem;
    background: var(--bg-surface);
    border-top: 1px solid var(--border);
    flex-shrink: 0;
}

.input-actions {
    display: flex;
    gap: .15rem;
    flex-shrink: 0;
    padding-bottom: .3rem;
}
.input-actions button {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: none;
    border: none;
    color: var(--text-dim);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    transition: all 0.2s ease;
    position: relative;
}
.input-actions button:hover {
    color: var(--primary-light);
    background: var(--glass-hover);
}

.msg-input-wrap {
    flex: 1;
    position: relative;
}
.msg-input-wrap textarea {
    width: 100%;
    resize: none;
    min-height: 40px;
    max-height: 120px;
    padding: .55rem .9rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    color: var(--text);
    font-size: .875rem;
    font-family: inherit;
    line-height: 1.45;
    transition: border-color .2s;
    overflow-y: auto;
}
.msg-input-wrap textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(99,102,241,0.1);
}
.msg-input-wrap textarea::placeholder { color: var(--text-dim); }

.send-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    flex-shrink: 0;
    box-shadow: 0 3px 12px rgba(99,102,241,0.35);
    transition: all 0.2s ease;
    margin-bottom: .3rem;
}
.send-btn:hover {
    transform: scale(1.06);
    box-shadow: 0 5px 16px rgba(99,102,241,0.5);
}
.send-btn:disabled {
    opacity: .4;
    cursor: not-allowed;
    transform: none;
}

/* ─── File Preview ─── */
.attachment-preview {
    display: none;
    padding: .5rem 1rem;
    background: var(--bg-surface);
    border-top: 1px solid var(--border);
}
.attachment-preview.visible { display: flex; }
.attachment-preview-inner {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .5rem .75rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 10px;
    flex: 1;
}
.attachment-preview img {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    object-fit: cover;
}
.attachment-preview-info {
    flex: 1;
    min-width: 0;
}
.attachment-preview-name {
    font-size: .8rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.attachment-preview-size {
    font-size: .68rem;
    color: var(--text-dim);
}
.attachment-remove {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(239,68,68,0.1);
    border: none;
    color: var(--danger);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    transition: var(--transition);
}
.attachment-remove:hover {
    background: rgba(239,68,68,0.2);
}

/* ─── Image Lightbox ─── */
.lightbox {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(0,0,0,.85);
    backdrop-filter: blur(12px);
    align-items: center;
    justify-content: center;
    padding: 2rem;
    cursor: pointer;
}
.lightbox.visible { display: flex; }
.lightbox img {
    max-width: 90vw;
    max-height: 85vh;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,.6);
}
.lightbox-close {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,.1);
    border: none;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ─── No Chat Selected ─── */
.no-chat-selected {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    gap: 1rem;
    text-align: center;
    padding: 2rem;
    background: var(--bg);
}
.no-chat-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: rgba(99,102,241,0.06);
    border: 2px dashed rgba(99,102,241,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--primary-light);
}

/* ─── Typing Indicator ─── */
.typing-indicator {
    display: none;
    align-self: flex-start;
}
.typing-indicator .msg-bubble {
    padding: .5rem .85rem;
    display: flex;
    gap: .25rem;
    align-items: center;
}
.typing-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--text-dim);
    animation: typingBounce 1.2s infinite;
}
.typing-dot:nth-child(2) { animation-delay: .15s; }
.typing-dot:nth-child(3) { animation-delay: .3s; }
@keyframes typingBounce {
    0%, 80%, 100% { transform: scale(.7); opacity: .4; }
    40% { transform: scale(1); opacity: 1; }
}

/* ─── Mobile Back Button ─── */
.mobile-back {
    display: none;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--glass);
    border: 1px solid var(--border);
    color: var(--text-muted);
    cursor: pointer;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    flex-shrink: 0;
}

/* ─── Responsive ─── */
@media (max-width: 768px) {
    .chat-sidebar {
        width: 100%;
        min-width: 100%;
        position: absolute;
        inset: 0;
        z-index: 10;
        transition: transform .3s ease;
    }
    .chat-sidebar.hidden-mobile {
        transform: translateX(-100%);
    }
    .mobile-back { display: flex; }
    .msg-row { max-width: 82%; }
    .chat-messages { padding: .75rem; }
    .chat-input-bar { padding: .5rem .75rem; }
    .msg-image { max-width: 200px; }
}
@media (min-width: 769px) and (max-width: 1024px) {
    .chat-sidebar { width: 280px; min-width: 260px; }
}
</style>
@endpush

@section('content')
<div class="chat-shell" style="position:relative;">

    {{-- ─── SIDEBAR ─── --}}
    <div class="chat-sidebar {{ isset($withUser) && $withUser ? 'hidden-mobile' : '' }}" id="chatSidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">
                <i class="fas fa-comments"></i>
                <span>
                    @if($authUser->isAdmin()) Chats @else Support @endif
                </span>
            </div>
            @if($authUser->isAdmin())
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" class="sidebar-search" id="contactSearch" placeholder="Search users…">
            </div>
            @endif
        </div>

        <div class="contacts-list" id="contactsList">
            @if($authUser->isAdmin())
                @forelse($allUsers as $u)
                <a href="{{ route('chat.index', ['with' => $u->id]) }}"
                   class="contact-item {{ isset($withUser) && $withUser && $withUser->id === $u->id ? 'active' : '' }}"
                   data-name="{{ strtolower($u->name) }}">
                    <div class="contact-avatar">
                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}">
                        <div class="contact-online"></div>
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">{{ $u->name }}</div>
                        <div class="contact-preview">{{ ucfirst($u->role ?? 'user') }}</div>
                    </div>
                </a>
                @empty
                <div style="padding:2rem;text-align:center;color:var(--text-dim);font-size:.85rem;">
                    <i class="fas fa-users" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.3;"></i>
                    No users yet.
                </div>
                @endforelse
            @else
                @if($withUser)
                <a href="{{ route('chat.index') }}" class="contact-item active">
                    <div class="contact-avatar">
                        <img src="{{ $withUser->avatar_url }}" alt="{{ $withUser->name }}">
                        <div class="contact-online"></div>
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">{{ $withUser->name }}</div>
                        <div class="contact-preview">Support Admin</div>
                    </div>
                </a>
                @else
                <div style="padding:2rem;text-align:center;color:var(--text-dim);font-size:.85rem;">
                    No admin available.
                </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ─── CHAT MAIN ─── --}}
    <div class="chat-main">
        @if(isset($withUser) && $withUser)

        {{-- Chat Header --}}
        <div class="chat-header">
            <button class="mobile-back" onclick="document.getElementById('chatSidebar').classList.remove('hidden-mobile')">
                <i class="fas fa-arrow-left"></i>
            </button>
            <img class="chat-header-avatar" src="{{ $withUser->avatar_url }}" alt="{{ $withUser->name }}">
            <div class="chat-header-info">
                <div class="chat-header-name">{{ $withUser->name }}</div>
                <div class="chat-header-status"><span class="dot"></span> Online</div>
            </div>
            <div class="chat-header-actions">
                <button title="Search"><i class="fas fa-search"></i></button>
                <button title="More"><i class="fas fa-ellipsis-vertical"></i></button>
            </div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chatMessages">
            @forelse($messages as $msg)
                @php $mine = $msg->sender_id === $authUser->id; @endphp
                <div class="msg-row {{ $mine ? 'mine' : 'theirs' }}" data-id="{{ $msg->id }}">
                    <div style="display:flex;flex-direction:column;gap:.1rem;max-width:100%;">
                        {{-- Image Attachment --}}
                        @if($msg->hasAttachment() && $msg->isImage())
                            <img src="{{ $msg->getAttachmentUrl() }}" alt="Image" class="msg-image" onclick="openLightbox(this.src)">
                        @endif

                        {{-- File Attachment --}}
                        @if($msg->hasAttachment() && $msg->isFile())
                            <a href="{{ $msg->getAttachmentUrl() }}" target="_blank" class="msg-file" download>
                                <div class="msg-file-icon"><i class="fas fa-file-alt"></i></div>
                                <div class="msg-file-info">
                                    <div class="msg-file-name">{{ $msg->attachment_name }}</div>
                                    <div class="msg-file-size">Click to download</div>
                                </div>
                                <i class="fas fa-download" style="color:var(--text-dim);font-size:.7rem;"></i>
                            </a>
                        @endif

                        {{-- Text Message --}}
                        @if($msg->message)
                            <div class="msg-bubble">{!! nl2br(e($msg->message)) !!}</div>
                        @endif

                        <div class="msg-time">
                            {{ $msg->created_at->format('h:i A') }}
                            @if($mine && $msg->isRead())
                                <i class="fas fa-check-double" style="color:var(--info);font-size:.55rem;margin-left:.2rem;"></i>
                            @elseif($mine)
                                <i class="fas fa-check" style="font-size:.55rem;margin-left:.2rem;"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="chat-empty">
                    <div class="chat-empty-icon"><i class="fas fa-paper-plane"></i></div>
                    <p>No messages yet. Say hello! 👋</p>
                </div>
            @endforelse

            {{-- Typing indicator --}}
            <div class="typing-indicator" id="typingDots">
                <div class="msg-row theirs">
                    <div class="msg-bubble" style="padding:.5rem .9rem;">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attachment Preview --}}
        <div class="attachment-preview" id="attachmentPreview">
            <div class="attachment-preview-inner">
                <img id="previewThumb" src="" alt="" style="display:none;">
                <div id="previewFileIcon" style="display:none;" class="msg-file-icon"><i class="fas fa-file-alt"></i></div>
                <div class="attachment-preview-info">
                    <div class="attachment-preview-name" id="previewName"></div>
                    <div class="attachment-preview-size" id="previewSize"></div>
                </div>
                <button class="attachment-remove" onclick="clearAttachment()" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Input Bar --}}
        <div class="chat-input-bar">
            <div class="input-actions">
                <button onclick="document.getElementById('fileInput').click()" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                </button>
                <button onclick="document.getElementById('imageInput').click()" title="Send image">
                    <i class="fas fa-image"></i>
                </button>
            </div>

            <input type="file" id="fileInput" style="display:none;" accept="*/*" onchange="handleFileSelect(this, 'file')">
            <input type="file" id="imageInput" style="display:none;" accept="image/*" onchange="handleFileSelect(this, 'image')">

            <div class="msg-input-wrap">
                <textarea
                    id="msgInput"
                    placeholder="Type a message..."
                    rows="1"
                    maxlength="2000"
                ></textarea>
            </div>

            <button class="send-btn" id="sendBtn" title="Send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>

        @else

        {{-- No Conversation Selected --}}
        <div class="no-chat-selected">
            <div class="no-chat-icon"><i class="fas fa-comments"></i></div>
            <div>
                <h3 style="font-weight:700;margin-bottom:.4rem;font-size:1.1rem;">
                    @if($authUser->isAdmin())
                        Select a conversation
                    @else
                        Chat with Support
                    @endif
                </h3>
                <p style="font-size:.85rem;color:var(--text-dim);line-height:1.6;max-width:320px;">
                    @if($authUser->isAdmin())
                        Choose a user from the sidebar to view or start a conversation.
                    @else
                        Our support team is here to help. Select admin from the sidebar.
                    @endif
                </p>
            </div>
        </div>

        @endif
    </div>

</div>

{{-- Image Lightbox --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close"><i class="fas fa-times"></i></button>
    <img id="lightboxImg" src="" alt="Full image">
</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Config
    const AUTH_ID   = {{ $authUser->id }};
    const WITH_USER = @json(isset($withUser) && $withUser ? $withUser->id : null);
    const SEND_URL  = "{{ route('chat.send') }}";
    const FETCH_URL = WITH_USER ? "{{ route('chat.fetch', ['user' => ':uid']) }}".replace(':uid', WITH_USER) : null;
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;

    if (!WITH_USER) return;

    // ── DOM refs
    const msgContainer = document.getElementById('chatMessages');
    const msgInput     = document.getElementById('msgInput');
    const sendBtn      = document.getElementById('sendBtn');

    // ── Attachment state
    let pendingFile = null;

    // ── Track last message ID
    let lastId = 0;
    document.querySelectorAll('[data-id]').forEach(el => {
        lastId = Math.max(lastId, parseInt(el.dataset.id) || 0);
    });
    scrollBottom();

    // ── Render message
    function renderMessage(msg) {
        const cls = msg.is_mine ? 'mine' : 'theirs';
        const empty = msgContainer.querySelector('.chat-empty');
        if (empty) empty.remove();

        const row = document.createElement('div');
        row.className = `msg-row ${cls}`;
        row.dataset.id = msg.id;

        let content = '<div style="display:flex;flex-direction:column;gap:.1rem;max-width:100%;">';

        // Image attachment
        if (msg.attachment && msg.attachment_type === 'image') {
            content += `<img src="${msg.attachment}" alt="Image" class="msg-image" onclick="openLightbox(this.src)">`;
        }

        // File attachment
        if (msg.attachment && msg.attachment_type === 'file') {
            content += `<a href="${msg.attachment}" target="_blank" class="msg-file" download>
                <div class="msg-file-icon"><i class="fas fa-file-alt"></i></div>
                <div class="msg-file-info">
                    <div class="msg-file-name">${escHtml(msg.attachment_name || 'File')}</div>
                    <div class="msg-file-size">Click to download</div>
                </div>
                <i class="fas fa-download" style="color:var(--text-dim);font-size:.7rem;"></i>
            </a>`;
        }

        // Text
        if (msg.message) {
            content += `<div class="msg-bubble">${escHtml(msg.message)}</div>`;
        }

        content += `<div class="msg-time">${msg.created_at}</div>`;
        content += '</div>';

        row.innerHTML = content;

        const typingDots = document.getElementById('typingDots');
        msgContainer.insertBefore(row, typingDots);
        scrollBottom();
    }

    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                  .replace(/"/g,'&quot;').replace(/'/g,'&#039;').replace(/\n/g,'<br>');
    }

    function scrollBottom() {
        requestAnimationFrame(() => {
            msgContainer.scrollTop = msgContainer.scrollHeight;
        });
    }

    // ── Send message
    async function sendMessage() {
        const text = msgInput.value.trim();
        if (!text && !pendingFile) return;

        sendBtn.disabled = true;
        msgInput.disabled = true;

        try {
            const formData = new FormData();
            formData.append('receiver_id', WITH_USER);
            if (text) formData.append('message', text);
            if (pendingFile) formData.append('attachment', pendingFile);

            const res = await fetch(SEND_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!res.ok) throw new Error('Send failed');
            const msg = await res.json();
            lastId = Math.max(lastId, msg.id);
            renderMessage(msg);
            msgInput.value = '';
            clearAttachment();
            autoResize();
        } catch (e) {
            console.error('Send error:', e);
        } finally {
            sendBtn.disabled = false;
            msgInput.disabled = false;
            msgInput.focus();
        }
    }

    // ── File handling
    window.handleFileSelect = function(input, type) {
        const file = input.files[0];
        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be under 10MB.');
            input.value = '';
            return;
        }

        pendingFile = file;

        const preview = document.getElementById('attachmentPreview');
        const thumb = document.getElementById('previewThumb');
        const fileIcon = document.getElementById('previewFileIcon');
        const name = document.getElementById('previewName');
        const size = document.getElementById('previewSize');

        name.textContent = file.name;
        size.textContent = formatFileSize(file.size);

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                thumb.src = e.target.result;
                thumb.style.display = 'block';
                fileIcon.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            thumb.style.display = 'none';
            fileIcon.style.display = 'flex';
        }

        preview.classList.add('visible');
        input.value = '';
    };

    window.clearAttachment = function() {
        pendingFile = null;
        document.getElementById('attachmentPreview').classList.remove('visible');
        document.getElementById('previewThumb').style.display = 'none';
        document.getElementById('previewFileIcon').style.display = 'none';
    };

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    // ── Lightbox
    window.openLightbox = function(src) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightbox').classList.add('visible');
    };
    window.closeLightbox = function() {
        document.getElementById('lightbox').classList.remove('visible');
    };

    // ── Poll for new messages
    async function pollMessages() {
        if (!FETCH_URL) return;
        try {
            const res = await fetch(`${FETCH_URL}?after=${lastId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            if (!res.ok) return;
            const data = await res.json();
            if (data.messages && data.messages.length) {
                data.messages.forEach(msg => {
                    if (!document.querySelector(`[data-id="${msg.id}"]`)) {
                        renderMessage(msg);
                        lastId = Math.max(lastId, msg.id);
                    }
                });
            }
        } catch (e) { /* silent */ }
    }

    setInterval(pollMessages, 2000);

    // ── Input handlers
    sendBtn.addEventListener('click', sendMessage);

    msgInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Auto-resize textarea
    function autoResize() {
        msgInput.style.height = 'auto';
        msgInput.style.height = Math.min(msgInput.scrollHeight, 120) + 'px';
    }
    msgInput.addEventListener('input', autoResize);

    // Sidebar search (admin)
    const searchInput = document.getElementById('contactSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.contact-item[data-name]').forEach(item => {
                item.style.display = item.dataset.name.includes(q) ? '' : 'none';
            });
        });
    }

    // ── Drag & drop
    msgContainer.addEventListener('dragover', (e) => {
        e.preventDefault();
        msgContainer.style.outline = '2px dashed var(--primary)';
        msgContainer.style.outlineOffset = '-4px';
    });
    msgContainer.addEventListener('dragleave', () => {
        msgContainer.style.outline = '';
        msgContainer.style.outlineOffset = '';
    });
    msgContainer.addEventListener('drop', (e) => {
        e.preventDefault();
        msgContainer.style.outline = '';
        msgContainer.style.outlineOffset = '';
        const file = e.dataTransfer.files[0];
        if (file) {
            // Create a fake input event
            const dt = new DataTransfer();
            dt.items.add(file);
            const fakeInput = { files: [file], value: '' };
            const type = file.type.startsWith('image/') ? 'image' : 'file';
            window.handleFileSelect(fakeInput, type);
        }
    });

    // Keyboard shortcut: Ctrl+V to paste images
    document.addEventListener('paste', (e) => {
        const items = e.clipboardData?.items;
        if (!items) return;
        for (const item of items) {
            if (item.type.startsWith('image/')) {
                e.preventDefault();
                const file = item.getAsFile();
                const fakeInput = { files: [file], value: '' };
                window.handleFileSelect(fakeInput, 'image');
                break;
            }
        }
    });

    // Initial scroll
    scrollBottom();
})();
</script>
@endpush
