<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show chat page.
     *
     * - Admin: sees sidebar with all users who have messaged, selects one to chat.
     * - User : only chats with the admin (first admin found).
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();

        if ($authUser->isAdmin()) {
            // Build list of users who have a conversation with admin
            $userIds = Message::where('sender_id', $authUser->id)
                ->orWhere('receiver_id', $authUser->id)
                ->get(['sender_id', 'receiver_id'])
                ->flatMap(fn($m) => [$m->sender_id, $m->receiver_id])
                ->unique()
                ->reject(fn($id) => $id === $authUser->id)
                ->values();

            $contacts = User::whereIn('id', $userIds)
                ->where('role', '!=', 'admin')
                ->get();

            // Also include all non-admin users so admin can initiate
            $allUsers = User::where('role', '!=', 'admin')
                ->orderBy('name')
                ->get();

            // Selected user to chat with
            $withUser = null;
            $messages = collect();
            if ($request->has('with')) {
                $withUser = User::findOrFail($request->with);
                $messages = Message::conversation($authUser->id, $withUser->id)->get();
                // Mark received messages as read
                Message::where('sender_id', $withUser->id)
                    ->where('receiver_id', $authUser->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }

            return view('chat.index', compact('allUsers', 'contacts', 'withUser', 'messages', 'authUser'));
        }

        // Regular user — find admin
        $admin = User::where('role', 'admin')->first();
        $messages = collect();
        $withUser = $admin;

        if ($admin) {
            $messages = Message::conversation($authUser->id, $admin->id)->get();
            // Mark admin replies as read
            Message::where('sender_id', $admin->id)
                ->where('receiver_id', $authUser->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('chat.index', compact('messages', 'withUser', 'authUser'));
    }

    /**
     * Send a message (AJAX).
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        $msg->load('sender');

        return response()->json([
            'id' => $msg->id,
            'message' => $msg->message,
            'sender_id' => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'sender_name' => $msg->sender->name,
            'sender_avatar' => $msg->sender->avatar_url,
            'created_at' => $msg->created_at->format('h:i A'),
            'is_mine' => true,
        ]);
    }

    /**
     * Poll for new messages after a given message ID (AJAX).
     */
    public function fetchMessages(Request $request, User $user)
    {
        $authUser = Auth::user();
        $afterId = (int)$request->get('after', 0);

        $messages = Message::conversation($authUser->id, $user->id)
            ->where('id', '>', $afterId)
            ->get()
            ->map(fn($m) => [
        'id' => $m->id,
        'message' => $m->message,
        'sender_id' => $m->sender_id,
        'receiver_id' => $m->receiver_id,
        'sender_name' => $m->sender->name,
        'sender_avatar' => $m->sender->avatar_url,
        'created_at' => $m->created_at->format('h:i A'),
        'is_mine' => $m->sender_id === $authUser->id,
        ]);

        // Mark as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    /**
     * Unread message count for current user (badge in navbar).
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}
