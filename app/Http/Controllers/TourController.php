<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{
    /**
     * Mark a specific tour as completed for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $type = $request->input('type', 'home'); // Default to home
        
        $allowedTypes = ['home', 'category', 'milestone'];
        if (!in_array($type, $allowedTypes)) {
            return response()->json(['success' => false, 'message' => 'Invalid tour type'], 400);
        }

        $column = "tour_{$type}_completed";
        $user->update([$column => true]);

        return response()->json(['success' => true]);
    }
}
