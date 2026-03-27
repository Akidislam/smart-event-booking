<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /** Show the user's support inbox + message form */
    public function index()
    {
        $messages = SupportMessage::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('support.index', compact('messages'));
    }

    /** Store a new support message */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:10|max:2000',
        ]);

        SupportMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your message has been sent! We will reply shortly.');
    }
}
