<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Milestone;
use App\Models\Action;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // 1. Create a dummy category
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Demo: Career Goals',
            'color_id' => 1,
            'icon_id' => 1,
        ]);

        // 2. Create a dummy goal inside this category
        $goal = Goal::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Become a Senior Executive',
            'description' => 'This is a demo goal. In Liftra, you can organize your long-term ambitions properly.',
            'target_age' => 30,
            'target_date' => now()->addYear(),
            'progress' => 0,
        ]);

        // 3. Create a dummy milestone
        $milestone = Milestone::create([
            'goal_id' => $goal->id,
            'title' => 'Complete Leadership Training',
            'description' => 'Milestones break down your massive goals into manageable checkpoints.',
            'due_date' => now()->addMonths(1),
        ]);

        // 4. Create a dummy action
        Action::create([
            'milestone_id' => $milestone->id,
            'title' => 'Read leadership book (Demo)',
            'due_date' => now(),
        ]);
        
        // Setup initial primary life goal
        $user->saveQuietly();
    }
}
