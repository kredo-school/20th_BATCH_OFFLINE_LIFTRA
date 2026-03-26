<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{
    /**
     * Mark the tour as completed for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete()
    {
        $user = Auth::user();
        if ($user) {
            $user->update(['has_completed_tour' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }
}
