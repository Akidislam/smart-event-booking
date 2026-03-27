<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    public function create(User $user): bool
    {
        // Any active authenticated user can list a venue.
        // They become a venue_owner by submitting the form; don't block them before.
        return $user->is_active !== false;
    }

    public function update(User $user, Venue $venue): bool
    {
        return $user->isAdmin() || $venue->user_id === $user->id;
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $user->isAdmin() || $venue->user_id === $user->id;
    }
}
