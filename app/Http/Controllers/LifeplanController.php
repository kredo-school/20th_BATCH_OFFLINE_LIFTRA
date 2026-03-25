<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Goal;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

class LifeplanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function storeCategory(Request $request)
    {
        // Idempotency for AI/AJAX requests: Avoid race condition validation errors
        if ($request->ajax()) {
            if ($request->filled('name')) {
                $existing = Category::where('name', $request->name)->where('user_id', Auth::id())->first();
                if ($existing) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Category accessed successfully.',
                        'category' => $existing
                    ]);
                }
            }
            
            // Randomize attributes if not provided by AI
            if (!$request->has('color_id')) {
                $request->merge(['color_id' => \App\Models\Color::inRandomOrder()->first()->id ?? 1]);
            }
            if (!$request->has('icon_id')) {
                $request->merge(['icon_id' => \App\Models\Icon::inRandomOrder()->first()->id ?? 1]);
            }
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'color_id' => 'required|exists:colors,id',
            'icon_id' => 'required|exists:icons,id',
        ], [
            'name.unique' => 'A category with this name already exists.',
        ]);

        $validated['user_id'] = Auth::id();
        $category = Category::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category added successfully!',
                'category' => $category
            ]);
        }

        return redirect()->route('home')->with('success', 'Category added successfully!');
    }

    public function updateCategory(Request $request, \App\Models\Category $category)
    {
        if ($category->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($category->id),
            ],
            'color_id' => 'required|exists:colors,id',
            'icon_id' => 'required|exists:icons,id',
        ], [
            'name.unique' => 'A category with this name already exists.',
        ]);

        $category->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'category' => $category
            ]);
        }

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function destroyCategory(Request $request, \App\Models\Category $category)
    {
        if ($category->user_id !== Auth::id()) abort(403);
        
        $category->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        }

        return redirect()->route('home')->with('success', 'Category deleted successfully!');
    }

    public function showCategory(\App\Models\Category $category)
    {
        if ($category->user_id !== Auth::id()) abort(403);

        $user = Auth::user();

        // Calculate user's current age from birthday
        $userAge = $user->birthday
            ? \Carbon\Carbon::parse($user->birthday)->age
            : null;

        // All categories for this user (for the category select in Add Goal modal)
        $userCategories = Category::where('user_id', Auth::id())->get();

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

        return view('lifeplan.category', compact(
            'category', 'goalsByDecade', 'overallProgress', 'totalMilestones', 'userAge', 'userCategories'
        ));
    }

    public function storeMilestone(Request $request)
    {
        $validated = $request->validate([
            'goal_id' => 'required|exists:goals,id',
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'action_titles' => 'nullable|array',
            'action_titles.*' => 'nullable|string|max:255',
            'action_dates' => 'nullable|array',
            'action_dates.*' => 'nullable|date',
        ]);

        // Ensure goal belongs to user via category
        $goal = Goal::findOrFail($validated['goal_id']);
        if ($goal->category->user_id != Auth::id()) abort(403);

        $milestone = \App\Models\Milestone::create([
            'goal_id' => $validated['goal_id'],
            'title' => $validated['title'],
            'due_date' => $validated['due_date'],
            'order' => $goal->milestones()->count() + 1, // Simple ordering
        ]);

        $titles = $request->input('action_titles', []);
        $dates = $request->input('action_dates', []);
        foreach ($titles as $index => $actionTitle) {
            if (!empty($actionTitle)) {
                $milestone->actions()->create([
                    'title' => $actionTitle,
                    'due_date' => $dates[$index] ?? null,
                    'completed' => false,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone added successfully!',
                'milestone' => $milestone->load('actions')
            ]);
        }

        return redirect()->back()->with('success', 'Milestone added successfully!');
    }

    public function updateMilestone(Request $request, \App\Models\Milestone $milestone)
    {
        if ($milestone->goal->category->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'new_action_titles' => 'nullable|array',
            'new_action_titles.*' => 'nullable|string|max:255',
            'new_action_dates' => 'nullable|array',
            'new_action_dates.*' => 'nullable|date',
            'actions' => 'nullable|array',
            'actions.*.title' => 'nullable|string|max:255',
            'actions.*.due_date' => 'nullable|date',
        ]);

        $milestone->update([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'],
            'completed_at' => $validated['completed_at'] ?? $milestone->completed_at,
        ]);

        // Handle existing actions
        if ($request->has('actions') && is_array($request->actions)) {
            foreach ($request->actions as $actionId => $actionData) {
                if (empty($actionData['title'])) {
                    $milestone->actions()->where('id', $actionId)->delete();
                } else {
                    $milestone->actions()->where('id', $actionId)->update([
                        'title' => $actionData['title'],
                        'due_date' => $actionData['due_date'] ?? null,
                    ]);
                }
            }
        }

        // Handle completely new actions appended during edit
        $newTitles = $request->input('new_action_titles', []);
        $newDates = $request->input('new_action_dates', []);
        foreach ($newTitles as $index => $actionTitle) {
            if (!empty($actionTitle)) {
                $milestone->actions()->create([
                    'title' => $actionTitle,
                    'due_date' => $newDates[$index] ?? null,
                    'completed' => false,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone updated successfully!',
                'milestone' => $milestone->load('actions')
            ]);
        }

        return redirect()->back()->with('success', 'Milestone updated successfully!');
    }

    public function destroyMilestone(Request $request, \App\Models\Milestone $milestone)
    {
        if ($milestone->goal->category->user_id !== Auth::id()) abort(403);

        $goal = $milestone->goal;
        $milestone->delete();

        // Recalculate progress after deletion
        $milestones = $goal->milestones()->with('actions')->get();
        $total = $milestones->count() + $milestones->flatMap->actions->count();
        $completed = $milestones->filter(fn($m) => !is_null($m->completed_at))->count() + $milestones->flatMap->actions->where('completed', true)->count();
        $goal->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        $goal->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Milestone deleted successfully!',
                'progress' => $goal->progress
            ]);
        }

        return redirect()->back()->with('success', 'Milestone deleted successfully!');
    }

    public function toggleMilestone(Request $request, \App\Models\Milestone $milestone)
    {
        if ($milestone->goal->category->user_id !== Auth::id()) abort(403);
        
        $milestone->completed_at = $milestone->completed_at ? null : now();
        $milestone->save();

        $goal = $milestone->goal;
        $milestones = $goal->milestones()->with('actions')->get();
        $total = $milestones->count() + $milestones->flatMap->actions->count();
        $completed = $milestones->filter(fn($m) => !is_null($m->completed_at))->count() + $milestones->flatMap->actions->where('completed', true)->count();
        $goal->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        $goal->save();
        
        return response()->json(['success' => true, 'progress' => $goal->progress]);
    }

    public function toggleAction(Request $request, \App\Models\Action $action)
    {
        if ($action->milestone->goal->category->user_id !== Auth::id()) abort(403);
        
        $action->completed = !$action->completed;
        $action->save();

        $goal = $action->milestone->goal;
        $milestones = $goal->milestones()->with('actions')->get();
        $total = $milestones->count() + $milestones->flatMap->actions->count();
        $completed = $milestones->filter(fn($m) => !is_null($m->completed_at))->count() + $milestones->flatMap->actions->where('completed', true)->count();
        $goal->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        $goal->save();
        
        return response()->json(['success' => true, 'progress' => $goal->progress]);
    }

    public function showGoal(\App\Models\Goal $goal)
    {
        // Ensure goal belongs to this user via category
        if ($goal->category->user_id !== Auth::id()) abort(403);

        $category = $goal->category->load('color', 'icon');

        // Load milestones with their actions
        $milestones = $goal->milestones()
            ->with('actions')
            ->orderBy('order')
            ->orderBy('due_date')
            ->get();

        // Progress counts
        $milestonesTotal    = $milestones->count();
        $milestonesCompleted = $milestones->filter(fn($m) => !is_null($m->completed_at))->count();
        $tasksTotal         = $milestones->flatMap->actions->count();
        $tasksCompleted     = $milestones->flatMap->actions->where('completed', true)->count();
        $totalItems         = $milestonesTotal + $tasksTotal;
        $completedItems     = $milestonesCompleted + $tasksCompleted;
        $goalProgress       = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        // Build timeline: collect completed milestones and completed actions, sorted newest first
        $timelineEvents = collect();

        foreach ($milestones as $milestone) {
            if ($milestone->completed_at) {
                $timelineEvents->push([
                    'date'         => $milestone->completed_at,
                    'title'        => $milestone->title,
                    'is_milestone' => true,
                ]);
            }
            foreach ($milestone->actions as $action) {
                if ($action->completed && $action->updated_at) {
                    $timelineEvents->push([
                        'date'         => $action->updated_at,
                        'title'        => $action->title,
                        'is_milestone' => false,
                    ]);
                }
            }
        }

        $timelineEvents = $timelineEvents->sortByDesc('date')->values();

        return view('lifeplan.milestone', compact(
            'goal', 'category', 'milestones',
            'milestonesTotal', 'milestonesCompleted',
            'tasksTotal', 'tasksCompleted',
            'goalProgress', 'timelineEvents'
        ));
    }

    public function storeGoal(Request $request)
    {
        $user = Auth::user();
        $userAge = $user->birthday
            ? \Carbon\Carbon::parse($user->birthday)->age
            : 0;

        // Auto-fill sometimes-forgotten AI inputs
        if ($request->ajax()) {
            if (!$request->has('target_age')) {
                $request->merge(['target_age' => $userAge + 1]);
            }
            if (!$request->has('target_date')) {
                $request->merge(['target_date' => \Carbon\Carbon::now()->addYear()->format('Y-m-d')]);
            }
            if (!$request->has('title') && $request->filled('category_name')) {
                $request->merge(['title' => 'General Goal in ' . $request->category_name]);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required_without:category_name|nullable|exists:categories,id',
            'category_name' => 'required_without:category_id|nullable|string|max:255',
            'target_age' => "required|integer|min:{$userAge}",
            'target_date' => 'required|date',
        ]);

        if ($request->filled('category_name')) {
            $category = Category::firstOrCreate(
                ['name' => $request->category_name, 'user_id' => Auth::id()],
                [
                    'color_id' => \App\Models\Color::inRandomOrder()->first()->id ?? 1, 
                    'icon_id' => \App\Models\Icon::inRandomOrder()->first()->id ?? 1
                ]
            );
            $validated['category_id'] = $category->id;
        }

        // Ensure the category belongs to this user
        $category = Category::findOrFail($validated['category_id']);
        if ($category->user_id !== Auth::id()) abort(403);

        $validated['user_id'] = Auth::id();
        $goal = Goal::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Goal added successfully!',
                'goal' => $goal
            ]);
        }

        return redirect()->back()->with('success', 'Goal added successfully!');
    }

    public function updateGoal(Request $request, \App\Models\Goal $goal)
    {
        if ($goal->user_id != Auth::id() && $goal->category->user_id != Auth::id()) abort(403);

        $user = Auth::user();
        $userAge = $user->birthday ? \Carbon\Carbon::parse($user->birthday)->age : 0;

        // Auto-fill sometimes-forgotten AI inputs
        if ($request->ajax()) {
            if (!$request->has('target_age')) {
                $request->merge(['target_age' => $userAge + 1]);
            }
            if (!$request->has('target_date')) {
                $request->merge(['target_date' => \Carbon\Carbon::now()->addYear()->format('Y-m-d')]);
            }
            if (!$request->has('title') && $request->filled('category_name')) {
                $request->merge(['title' => 'General Goal in ' . $request->category_name]);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required_without:category_name|nullable|exists:categories,id',
            'category_name' => 'required_without:category_id|nullable|string|max:255',
            'target_age' => "required|integer|min:{$userAge}",
            'target_date' => 'required|date',
        ]);

        if ($request->filled('category_name')) {
            $category = Category::firstOrCreate(
                ['name' => $request->category_name, 'user_id' => Auth::id()],
                ['color_id' => 1, 'icon_id' => 1]
            );
            $validated['category_id'] = $category->id;
        }

        // Ensure the selected category belongs to this user
        $category = Category::findOrFail($validated['category_id']);
        if ($category->user_id != Auth::id()) abort(403);

        $goal->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Goal updated successfully!',
                'goal' => $goal
            ]);
        }

        return redirect()->back()->with('success', 'Goal updated successfully!');
    }

    public function destroyGoal(Request $request, \App\Models\Goal $goal)
    {
        if ($goal->user_id != Auth::id() && $goal->category->user_id != Auth::id()) abort(403);

        $goal->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Goal deleted successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Goal deleted successfully!');
    }
}
