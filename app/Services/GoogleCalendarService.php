<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use App\Models\User;
use App\Models\CalendarEvent;
use Carbon\Carbon;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR_READONLY);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Sync Google Calendar events for a user.
     */
    public function syncEvents(User $user)
    {
        if (!$user->google_access_token) {
            return false;
        }

        $this->client->setAccessToken([
            'access_token' => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
            'expires_in' => $user->google_token_expires_at ? Carbon::parse($user->google_token_expires_at)->diffInSeconds(now()) : 0,
        ]);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                if (isset($newToken['error'])) {
                     return false;
                }
                $user->update([
                    'google_access_token' => $newToken['access_token'],
                    'google_token_expires_at' => now()->addSeconds($newToken['expires_in']),
                ]);
            } else {
                return false;
            }
        }

        $service = new Calendar($this->client);
        $calendarId = 'primary';
        $optParams = [
            'maxResults' => 50,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => now()->subMonths(1)->toRfc3339String(),
        ];
        
        try {
            $results = $service->events->listEvents($calendarId, $optParams);
            $events = $results->getItems();

            foreach ($events as $event) {
                $start = $event->start->dateTime ?? $event->start->date;
                $end = $event->end->dateTime ?? $event->end->date;

                CalendarEvent::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'google_event_id' => $event->id,
                    ],
                    [
                        'title' => $event->getSummary() ?? '(No Title)',
                        'start_date' => Carbon::parse($start),
                        'end_date' => $end ? Carbon::parse($end) : null,
                        'source' => 'google',
                        'is_synced' => true,
                    ]
                );
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Calendar Sync Error: ' . $e->getMessage());
            return false;
        }
    }
}
