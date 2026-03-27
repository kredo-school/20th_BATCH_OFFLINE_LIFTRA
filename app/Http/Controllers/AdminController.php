<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Habit;
use App\Models\Task;
use App\Models\Journal;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

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

    public function show(User $user)
    {
        $stats = [
            'habits_count' => Habit::where('user_id', $user->id)->count(),
            'tasks_count' => Task::where('user_id', $user->id)->count(),
            'journals_count' => Journal::where('user_id', $user->id)->count(),
        ];
        
        $totalTasks = $stats['tasks_count'];
        $completedTasks = Task::where('user_id', $user->id)->where('completed', true)->count();
        $stats['completion_rate'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return view('admin.users_show', compact('user', 'stats'));
    }

    public function toggleRole(User $user)
    {
        // Prevent removing own admin privileges to avoid lockout
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot demote yourself.');
        }

        $user->role_id = $user->role_id === 1 ? 0 : 1;
        $user->save();

        return back()->with('success', "{$user->name}'s role has been updated.");
    }

    public function toggleSuspend(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $action = $user->is_suspended ? 'suspended' : 'restored';
        return back()->with('success', "{$user->name}'s account has been {$action}.");
    }

    public function sendPasswordReset(User $user)
    {
        $token = Password::getRepository()->create($user);
        $user->sendPasswordResetNotification($token);
        
        return back()->with('success', "Password reset link sent to {$user->email}.");
    }
}
