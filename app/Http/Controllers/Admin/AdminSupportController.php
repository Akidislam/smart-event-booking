<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class AdminSupportController extends Controller
{
    /** List all support messages */
    public function index(Request $request)
    {
        $query = SupportMessage::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->paginate(20);
        $pendingCount = SupportMessage::where('status', 'pending')->count();

        return view('admin.support', compact('messages', 'pendingCount'));
    }

    /** Post a reply to a specific message */
    public function reply(Request $request, SupportMessage $message)
    {
        $request->validate([
            'reply' => 'required|string|min:2|max:2000',
        ]);

        $message->update([
            'reply' => $request->reply,
            'status' => 'replied',
        ]);

        return back()->with('success', 'Reply sent to ' . $message->user->name . '.');
    }
}
