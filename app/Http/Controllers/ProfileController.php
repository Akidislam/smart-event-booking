<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /** Show the authenticated user's profile */
    public function index()
    {
        $user = Auth::user()->load(['events' => function ($q) {
            $q->latest()->take(5);
        }, 'bookings' => function ($q) {
            $q->with(['event', 'venue'])->latest()->take(5);
        }]);

        $stats = [
            'events' => Auth::user()->events()->count(),
            'bookings' => Auth::user()->bookings()->count(),
            'venues' => Auth::user()->venues()->count(),
            'pending' => Auth::user()->bookings()->where('status', 'pending')->count(),
        ];

        return view('profile.index', compact('user', 'stats'));
    }

    /** Show the profile edit form */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /** Update the user's profile */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully!');
    }
}
