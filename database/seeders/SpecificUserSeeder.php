<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\Journal;
use App\Models\Habit;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserCertification;
use App\Models\UserSkill;

class SpecificUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = [3, 6];

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            
            if (!$user) {
                $this->command->info("User with ID {$userId} not found. Skipping.");
                continue;
            }

            $this->command->info("Seeding dummy data for User ID: {$userId} ({$user->name})");

            // 1. Categories, Goals, and Milestones
            Category::factory(3)->create(['user_id' => $user->id])->each(function ($category) use ($user) {
                Goal::factory(2)->create([
                    'user_id' => $user->id,
                    'category_id' => $category->id
                ])->each(function ($goal) {
                    Milestone::factory(2)->create([
                        'goal_id' => $goal->id
                    ]);
                });
            });

            // 2. Tasks
            Task::factory(10)->create(['user_id' => $user->id]);

            // 3. Journals
            Journal::factory(15)->sequence(fn ($sq) => [
                'entry_date' => now()->subDays($sq->index)->format('Y-m-d'),
                'user_id' => $user->id
            ])->create();

            // 4. Habits
            Habit::factory(3)->create(['user_id' => $user->id]);

            // 5. Professional Profile
            UserEducation::factory(2)->create(['user_id' => $user->id]);
            UserExperience::factory(3)->create(['user_id' => $user->id]);
            UserCertification::factory(2)->create(['user_id' => $user->id]);
            UserSkill::factory(5)->create(['user_id' => $user->id]);

            $this->command->info("Successfully seeded data for User ID: {$userId}");
        }
    }
}
