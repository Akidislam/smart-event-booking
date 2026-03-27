@extends('layouts.app')

@section('title', 'Live Chat — EventVenue')

@push('styles')
<style>
/* ─── Chat Shell ─── */
.chat-shell {
    height: calc(100vh - var(--nav-h) - 1px);
    display: flex;
    overflow: hidden;
    background: var(--bg);
}

/* ─── Sidebar ─── */
.chat-sidebar {
    width: 300px;
    min-width: 260px;
    background: var(--bg-surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}
.sidebar-header {
    padding: 1.25rem 1.25rem 1rem;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.sidebar-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .75rem;
}
.sidebar-title i { color: var(--primary-light); }
.sidebar-search {
    width: 100%;
    padding: .55rem .9rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 50px;
    color: var(--text);
    font-size: .85rem;
    font-family: inherit;
}
.sidebar-search:focus {
    outline: none;
    border-color: var(--primary);
}
.sidebar-search::placeholder { color: var(--text-dim); }

.contacts-list {
    flex: 1;
    overflow-y: auto;
    padding: .5rem;
}
.contacts-list::-webkit-scrollbar { width: 4px; }
.contacts-list::-webkit-scrollbar-track { background: transparent; }
.contacts-list::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 4px; }

.contact-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem 1rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    text-decoration: none;
    transition: var(--transition);
    position: relative;
}
.contact-item:hover { background: var(--glass-hover); }
.contact-item.active {
    background: rgba(99,102,241,0.15);
    border: 1px solid rgba(99,102,241,0.25);
}
.contact-avatar {
    position: relative;
    flex-shrink: 0;
}
.contact-avatar img {
    width: 44px; height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border);
}
.contact-item.active .contact-avatar img { border-color: var(--primary); }
.contact-unread {
    position: absolute;
    top: -2px; right: -2px;
    width: 16px; height: 16px;
    background: var(--secondary);
    border-radius: 50%;
    font-size: .65rem;
    font-weight: 700;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--bg-surface);
}
.contact-info { flex: 1; min-width: 0; }
.contact-name {
    font-size: .875rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.contact-role {
    font-size: .75rem;
    color: var(--text-dim);
    text-transform: capitalize;
}
.contact-item.active .contact-name { color: var(--primary-light); }

/* ─── Chat Main ─── */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
}

/* Chat header */
.chat-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.chat-header img {
    width: 42px; height: 42px;
    border-radius: 50%;
    border: 2px solid var(--primary);
    object-fit: cover;
}
.chat-header-name {
    font-weight: 700;
    font-size: .95rem;
}
.chat-header-status {
    font-size: .78rem;
    color: var(--success);
    display: flex;
    align-items: center;
    gap: .3rem;
}
.chat-header-status::before {
    content: '';
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--success);
    display: inline-block;
}

/* Messages area */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
    scroll-behavior: smooth;
}
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

/* Message rows */
.msg-row {
    display: flex;
    align-items: flex-end;
    gap: .6rem;
    max-width: 70%;
    animation: msgPop .2s ease;
}
@keyframes msgPop {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.msg-row.mine {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.msg-row.theirs {
    align-self: flex-start;
}
.msg-row-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid var(--border);
}
.msg-col {
    display: flex;
    flex-direction: column;
    gap: .2rem;
}
.msg-sender-name {
    font-size: .72rem;
    font-weight: 600;
    color: var(--text-dim);
    padding: 0 .75rem;
}
.msg-row.mine .msg-sender-name { text-align: right; color: var(--primary-light); }

.msg-bubble {
    padding: .65rem 1rem;
    border-radius: 18px;
    font-size: .9rem;
    line-height: 1.55;
    word-break: break-word;
    position: relative;
}
.msg-row.mine .msg-bubble {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
    border-bottom-right-radius: 4px;
    box-shadow: 0 4px 15px rgba(99,102,241,0.35);
}
.msg-row.theirs .msg-bubble {
    background: var(--bg-card);
    color: var(--text);
    border: 1px solid var(--border);
    border-bottom-left-radius: 4px;
}
.msg-time {
    font-size: .68rem;
    color: var(--text-dim);
    padding: 0 .75rem;
    align-self: flex-end;
}
.msg-row.mine .msg-time { text-align: right; }

/* Date separator */
.date-sep {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: .5rem 0;
}
.date-sep::before, .date-sep::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}
.date-sep span {
    font-size: .72rem;
    color: var(--text-dim);
    white-space: nowrap;
    font-weight: 600;
}

/* Empty state */
.chat-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    gap: 1rem;
    padding: 2rem;
    text-align: center;
}
.chat-empty i { font-size: 4rem; opacity: .25; }
.chat-empty p { font-size: 1.05rem; max-width: 360px; line-height: 1.6; }

/* Input bar */
.chat-input-bar {
    display: flex;
    align-items: flex-end;
    gap: .75rem;
    padding: 1rem 1.5rem;
    background: var(--bg-surface);
    border-top: 1px solid var(--border);
    flex-shrink: 0;
}
.chat-input-bar textarea {
    flex: 1;
    resize: none;
    min-height: 44px;
    max-height: 140px;
    padding: .7rem 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 22px;
    color: var(--text);
    font-size: .9rem;
    font-family: inherit;
    line-height: 1.5;
    transition: border-color .2s;
    overflow-y: auto;
}
.chat-input-bar textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}
.chat-input-bar textarea::placeholder { color: var(--text-dim); }
.send-btn {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(99,102,241,0.4);
    transition: var(--transition);
}
.send-btn:hover { transform: scale(1.08); box-shadow: 0 6px 20px rgba(99,102,241,0.55); }
.send-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

/* No chat selected */
.no-chat-selected {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    gap: 1.25rem;
    text-align: center;
    padding: 2rem;
    background: var(--bg);
}
.no-chat-selected .icon-wrap {
    width: 100px; height: 100px;
    border-radius: 50%;
    background: rgba(99,102,241,0.08);
    border: 2px dashed rgba(99,102,241,0.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem;
    color: var(--primary-light);
}

/* Typing indicator */
.typing-dots {
    display: none;
    align-items: center;
    gap: .3rem;
    padding: .5rem .75rem;
    max-width: 70px;
}
.typing-dots span {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--text-dim);
    animation: typingDot 1.2s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: .2s; }
.typing-dots span:nth-child(3) { animation-delay: .4s; }
@keyframes typingDot {
    0%, 80%, 100% { transform: scale(.75); opacity: .5; }
    40% { transform: scale(1); opacity: 1; }
}

/* Mobile */
@media (max-width: 640px) {
    .chat-sidebar { width: 72px; min-width: 72px; }
    .sidebar-title span, .sidebar-search,
    .contact-info, .sidebar-header h2 { display: none; }
    .sidebar-header { padding: .75rem; }
    .contact-item { padding: .6rem; justify-content: center; }
    .chat-messages { padding: 1rem; }
    .chat-input-bar { padding: .75rem 1rem; }
    .msg-row { max-width: 88%; }
}
</style>
@endpush

@section('content')
<div class="chat-shell">

    {{-- ─── SIDEBAR ─── --}}
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">
                <i class="fas fa-comments"></i>
                <span>
                    @if($authUser->isAdmin()) All Users @else Support Chat @endif
                </span>
            </div>
            @if($authUser->isAdmin())
            <input type="text" class="sidebar-search" id="contactSearch" placeholder="Search users…">
            @endif
        </div>

        <div class="contacts-list" id="contactsList">
            @if($authUser->isAdmin())
                @forelse($allUsers as $u)
                <a href="{{ route('chat.index', ['with' => $u->id]) }}"
                   class="contact-item {{ isset($withUser) && $withUser->id === $u->id ? 'active' : '' }}"
                   data-name="{{ strtolower($u->name) }}">
                    <div class="contact-avatar">
                        <img class="w-full h-auto object-cover w-full h-auto object-cover" src="{{ $u->avatar_url }}" alt="{{ $u->name }}">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">{{ $u->name }}</div>
                        <div class="contact-role">{{ $u->role ?? 'user' }}</div>
                    </div>
                </a>
                @empty
                <div style="padding:1.5rem;text-align:center;color:var(--text-dim);font-size:.85rem;">
                    No users yet.
                </div>
                @endforelse
            @else
                {{-- Regular user only sees the admin --}}
                @if($withUser)
                <a href="{{ route('chat.index') }}" class="contact-item active">
                    <div class="contact-avatar">
                        <img class="w-full h-auto object-cover w-full h-auto object-cover" src="{{ $withUser->avatar_url }}" alt="{{ $withUser->name }}">
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">{{ $withUser->name }}</div>
                        <div class="contact-role">Support Admin</div>
                    </div>
                </a>
                @else
                <div style="padding:1.5rem;text-align:center;color:var(--text-dim);font-size:.85rem;">
                    No admin found.
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
            <img class="w-full h-auto object-cover w-full h-auto object-cover" src="{{ $withUser->avatar_url }}" alt="{{ $withUser->name }}">
            <div>
                <div class="chat-header-name">{{ $withUser->name }}</div>
                <div class="chat-header-status">Online</div>
            </div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chatMessages">
            @forelse($messages as $msg)
                @php $mine = $msg->sender_id === $authUser->id; @endphp
                <div class="msg-row {{ $mine ? 'mine' : 'theirs' }}" data-id="{{ $msg->id }}">
                    <img class="msg-row-avatar w-full h-auto object-cover w-full h-auto object-cover" src="{{ $msg->sender->avatar_url }}" alt="{{ $msg->sender->name }}">
                    <div class="msg-col">
                        <div class="msg-sender-name">{{ $mine ? 'You' : $msg->sender->name }}</div>
                        <div class="msg-bubble">{{ $msg->message }}</div>
                        <div class="msg-time">{{ $msg->created_at->format('h:i A') }}</div>
                    </div>
                </div>
            @empty
                <div class="chat-empty">
                    <i class="fas fa-comment-dots"></i>
                    <p>No messages yet. Say hello! 👋</p>
                </div>
            @endforelse

            {{-- Typing indicator (hidden by default) --}}
            <div class="typing-dots" id="typingDots" style="align-self:flex-start;">
                <div class="msg-row theirs">
                    <div class="msg-bubble" style="padding:.45rem .9rem;">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input bar --}}
        <div class="chat-input-bar">
            <textarea
                id="msgInput"
                placeholder="Type a message… (Enter to send, Shift+Enter for new line)"
                rows="1"
                maxlength="2000"
            ></textarea>
            <button class="send-btn" id="sendBtn" title="Send message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>

        @else

        {{-- No conversation selected --}}
        <div class="no-chat-selected">
            <div class="icon-wrap"><i class="fas fa-comments"></i></div>
            <div>
                <h3 style="font-weight:700;margin-bottom:.5rem;">
                    @if($authUser->isAdmin())
                        Select a user to start chatting
                    @else
                        Start a chat with Support
                    @endif
                </h3>
                <p style="font-size:.9rem;color:var(--text-dim);line-height:1.6;">
                    @if($authUser->isAdmin())
                        Choose a user from the sidebar to view or start a conversation.
                    @else
                        Our support team is here to help. Pick the admin from the sidebar.
                    @endif
                </p>
            </div>
        </div>

        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Config ──────────────────────────────────────────────
    const AUTH_ID     = {{ $authUser->id }};
    const WITH_USER   = @json(isset($withUser) && $withUser ? $withUser->id : null);
    const SEND_URL    = "{{ route('chat.send') }}";
    const FETCH_URL   = WITH_USER ? "{{ route('chat.fetch', ['user' => ':uid']) }}".replace(':uid', WITH_USER) : null;
    const CSRF        = document.querySelector('meta[name="csrf-token"]').content;

    if (!WITH_USER) return; // no conversation selected

    // ── DOM refs ────────────────────────────────────────────
    const msgContainer = document.getElementById('chatMessages');
    const msgInput     = document.getElementById('msgInput');
    const sendBtn      = document.getElementById('sendBtn');

    // ── Track last message ID for polling ───────────────────
    let lastId = 0;
    document.querySelectorAll('[data-id]').forEach(el => {
        lastId = Math.max(lastId, parseInt(el.dataset.id) || 0);
    });
    scrollBottom();

    // ── Render a single message bubble ──────────────────────
    function renderMessage(msg) {
        const cls = msg.is_mine ? 'mine' : 'theirs';

        // Remove empty state if present
        const empty = msgContainer.querySelector('.chat-empty');
        if (empty) empty.remove();

        const row = document.createElement('div');
        row.className = `msg-row ${cls}`;
        row.dataset.id = msg.id;
        row.innerHTML = `
            <img class="msg-row-avatar w-full h-auto object-cover w-full h-auto object-cover" src="${msg.sender_avatar}" alt="${msg.sender_name}">
            <div class="msg-col">
                <div class="msg-sender-name">${msg.is_mine ? 'You' : msg.sender_name}</div>
                <div class="msg-bubble">${escHtml(msg.message)}</div>
                <div class="msg-time">${msg.created_at}</div>
            </div>
        `;
        // Insert before the typing dots
        const typingDots = document.getElementById('typingDots');
        msgContainer.insertBefore(row, typingDots);
        scrollBottom();
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                  .replace(/"/g,'&quot;').replace(/'/g,'&#039;').replace(/\n/g,'<br>');
    }

    function scrollBottom() {
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    // ── Send message ─────────────────────────────────────────
    async function sendMessage() {
        const text = msgInput.value.trim();
        if (!text) return;

        sendBtn.disabled = true;
        msgInput.disabled = true;

        try {
            const res = await fetch(SEND_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ receiver_id: WITH_USER, message: text }),
            });

            if (!res.ok) throw new Error('Send failed');
            const msg = await res.json();
            lastId = Math.max(lastId, msg.id);
            renderMessage(msg);
            msgInput.value = '';
            autoResize();
        } catch (e) {
            console.error('Send error:', e);
        } finally {
            sendBtn.disabled = false;
            msgInput.disabled = false;
            msgInput.focus();
        }
    }

    // ── Poll for new messages every 2s ──────────────────────
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
                    // Skip messages we already rendered
                    if (!document.querySelector(`[data-id="${msg.id}"]`)) {
                        renderMessage(msg);
                        lastId = Math.max(lastId, msg.id);
                    }
                });
            }
        } catch (e) { /* silent */ }
    }

    setInterval(pollMessages, 2000);

    // ── Input handlers ──────────────────────────────────────
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
        msgInput.style.height = Math.min(msgInput.scrollHeight, 140) + 'px';
    }
    msgInput.addEventListener('input', autoResize);

    // ── Sidebar search (admin) ──────────────────────────────
    const searchInput = document.getElementById('contactSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.contact-item[data-name]').forEach(item => {
                item.style.display = item.dataset.name.includes(q) ? '' : 'none';
            });
        });
    }

    // Initial scroll
    scrollBottom();
})();
</script>
@endpush
