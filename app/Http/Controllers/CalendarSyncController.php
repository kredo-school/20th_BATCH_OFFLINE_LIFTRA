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
            return redirect()->route('google.login')->with('error', 'Google re-authentication required.');
        }

        $success = $this->calendarService->syncEvents($user);

        if ($success) {
            return back()->with('success', 'Google Calendar synchronized successfully!');
        }

        return back()->with('error', 'Failed to synchronize Google Calendar.');
    }
}
