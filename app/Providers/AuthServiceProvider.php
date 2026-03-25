<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Venue;
use App\Policies\BookingPolicy;
use App\Policies\EventPolicy;
use App\Policies\VenuePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Venue::class => VenuePolicy::class ,
        Event::class => EventPolicy::class ,
        Booking::class => BookingPolicy::class ,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
