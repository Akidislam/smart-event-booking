<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime;

class GoogleCalendarService
{
    protected function getClient(User $user): ?Client
    {
        if (!$user->google_token) {
            return null;
        }

        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken([
            'access_token' => $user->google_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in' => 3600,
        ]);

        if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $user->update(['google_token' => $newToken['access_token'] ?? $user->google_token]);
        }

        return $client;
    }

    public function createEvent(Event $event, User $user): ?string
    {
        $client = $this->getClient($user);
        if (!$client)
            return null;

        $service = new Calendar($client);
        $location = $event->venue ? $event->venue->address . ', ' . $event->venue->city : '';

        $googleEvent = new GoogleEvent([
            'summary' => $event->title,
            'description' => $event->description ?? '',
            'location' => $location,
            'start' => new EventDateTime([
                'dateTime' => $event->start_datetime->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ]),
            'end' => new EventDateTime([
                'dateTime' => $event->end_datetime->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ]),
        ]);

        $result = $service->events->insert('primary', $googleEvent);
        return $result->getId();
    }

    public function updateEvent(Event $event, User $user): void
    {
        $client = $this->getClient($user);
        if (!$client || !$event->google_calendar_event_id)
            return;

        $service = new Calendar($client);
        $location = $event->venue ? $event->venue->address . ', ' . $event->venue->city : '';

        $googleEvent = $service->events->get('primary', $event->google_calendar_event_id);
        $googleEvent->setSummary($event->title);
        $googleEvent->setDescription($event->description ?? '');
        $googleEvent->setLocation($location);
        $googleEvent->setStart(new EventDateTime([
            'dateTime' => $event->start_datetime->toRfc3339String(),
            'timeZone' => config('app.timezone'),
        ]));
        $googleEvent->setEnd(new EventDateTime([
            'dateTime' => $event->end_datetime->toRfc3339String(),
            'timeZone' => config('app.timezone'),
        ]));

        $service->events->update('primary', $event->google_calendar_event_id, $googleEvent);
    }

    public function deleteEvent(Event $event, User $user): void
    {
        $client = $this->getClient($user);
        if (!$client || !$event->google_calendar_event_id)
            return;

        $service = new Calendar($client);
        $service->events->delete('primary', $event->google_calendar_event_id);
    }

    public function createBookingEvent(Booking $booking, User $user): ?string
    {
        $client = $this->getClient($user);
        if (!$client)
            return null;

        $service = new Calendar($client);
        $title = $booking->venue ? 'Venue Booking: ' . $booking->venue->name : 'Event Booking';
        if ($booking->event) {
            $title = 'Event: ' . $booking->event->title;
        }

        $googleEvent = new GoogleEvent([
            'summary' => $title,
            'description' => 'Booking Reference: ' . $booking->booking_reference,
            'location' => $booking->venue ? ($booking->venue->address . ', ' . $booking->venue->city) : '',
            'start' => new EventDateTime([
                'dateTime' => $booking->start_datetime->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ]),
            'end' => new EventDateTime([
                'dateTime' => $booking->end_datetime->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ]),
        ]);

        $result = $service->events->insert('primary', $googleEvent);
        return $result->getId();
    }

    public function deleteBookingEvent(Booking $booking, User $user): void
    {
        $client = $this->getClient($user);
        if (!$client || !$booking->google_calendar_event_id)
            return;

        $service = new Calendar($client);
        $service->events->delete('primary', $booking->google_calendar_event_id);
    }
}
