<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class LifeplanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_id' => 'required|exists:colors,id',
            'icon_id' => 'required|exists:icons,id',
        ]);

        $validated['user_id'] = Auth::id();
        Category::create($validated);

        return redirect()->route('home')->with('success', 'Category added successfully!');
    }

    public function showCategory(\App\Models\Category $category)
    {
        if ($category->user_id !== Auth::id()) abort(403);

        // Load goals with milestones, sorted by target_age
        $goals = $category->goals()
            ->with('milestones')
            ->orderBy('target_age')
            ->get();

        // Group by decade string: 20s, 30s, 40s etc.
        $goalsByDecade = $goals->groupBy(function ($goal) {
            $decade = floor($goal->target_age / 10) * 10;
            return $decade . 's';
        });

        $totalMilestones = $goals->flatMap->milestones->count();
        $completedMilestones = $goals->flatMap->milestones->filter(fn($m) => !is_null($m->completed_at))->count();
        $overallProgress = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;

        return view('lifeplan.category', compact('category', 'goalsByDecade', 'overallProgress', 'totalMilestones'));
    }
}
