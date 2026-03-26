<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    private $category;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Category $category)
    {
        $this->middleware('auth');
        $this->category = $category;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::id();

        $categories = $this->category
            ->where('user_id', $userId)
            ->with(['goals', 'color', 'icon'])
            ->get();

        $totalGoals = 0;
        $completedGoals = 0;

        foreach ($categories as $category) {
            foreach ($category->goals as $goal) {
                $totalGoals++;
                if ($goal->progress >= 100) {
                    $completedGoals++;
                }
            }
        }

        $overallProgress = $totalGoals > 0 ? round(($completedGoals / $totalGoals) * 100) : 0;

        return view('home', compact('categories', 'overallProgress'));
    }
}
