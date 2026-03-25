<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    public function create(User $user): bool
    {
        return $user->isVenueOwner();
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
