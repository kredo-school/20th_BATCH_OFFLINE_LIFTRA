<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Habit;
use App\Models\Task;
use App\Models\Journal;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_habits' => Habit::count(),
            'total_tasks' => Task::count(),
            'total_journals' => Journal::count(),
            'recent_users' => User::orderBy('created_at', 'desc')->take(5)->get()
        ];
        
        return view('admin.dashboard', compact('stats'));
    }

    public function users(Request $request)
    {
        $query = User::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('admin.users', compact('users'));
    }
}
