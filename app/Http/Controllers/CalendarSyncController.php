<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Auth;

class CalendarSyncController extends Controller
{
    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function sync()
    {
        $user = Auth::user();
        
        if (!$user->google_refresh_token) {
            return back()->with('error', __('To sync with Google Calendar, please log out and log in again with your Google account.'));
        }

        $result = $this->calendarService->syncEvents($user);

        if ($result['success']) {
            return back()->with('success', __('Google Calendar synchronized successfully! (:count events)', ['count' => $result['count']]));
        }

        return back()->with('error', __('Failed to synchronize Google Calendar.'));
    }
}
