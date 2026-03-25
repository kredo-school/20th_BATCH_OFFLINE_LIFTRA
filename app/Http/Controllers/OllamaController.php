<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OllamaController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'model' => 'nullable|string',
            'history' => 'nullable|array',
        ]);

        // Determine the best available model
        $model = $this->getAvailableModel($request->model ?? 'translategemma:latest');
        $userPrompt = $request->prompt;
        $history = $request->input('history', []);

        // --- ユーザー情報の取得とコンテキスト作成 ---
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ログインが必要です。',
            ], 401);
        }

        // 目標とそのマイルストーン (Goals with Milestones)
        $goals = $user->goals()->with('milestones')->get();
        $goalStrings = $goals->map(function($g) {
            $ms = $g->milestones->map(fn($m) => "  - Milestone: \"{$m->title}\" (ID: {$m->id})" . ($m->due_date ? " (Due: {$m->due_date->format('Y/m/d')})" : '') . ($m->completed_at ? " [COMPLETED]" : ''))->join("\n");
            return "- Goal: \"{$g->title}\" (ID: {$g->id}, Category ID: {$g->category_id})" . ($g->target_age ? " [Target Age: {$g->target_age}]" : '') . ($ms ? "\n$ms" : '');
        })->join("\n");

        // ライフカテゴリー (Categories)
        $categories = $user->categories;
        $categoryStrings = $categories->map(fn($c) => "- Category: \"{$c->name}\" (ID: {$c->id})")->join("\n");

        // ジャーナル (Journals - recent 5)
        $journals = $user->journals()->orderBy('entry_date', 'desc')->limit(5)->get();
        $journalStrings = $journals->map(fn($j) => "- Journal: \"{$j->title}\" (ID: {$j->id}) - Date: {$j->entry_date}")->join("\n");

        // カレンダー予定 (Calendar Events - logic for today/upcoming)
        $events = $user->calendarEvents()->where('start_date', '>=', today())->orderBy('start_date')->limit(10)->get();
        $eventStrings = $events->map(fn($e) => "- Event: \"{$e->title}\" (ID: {$e->id}) [Start: " . $e->start_date . ($e->end_date ? " End: " . $e->end_date : '') . "]")->join("\n");

        // 習慣情報
        $habits = $user->habits;
        $activeHabits = $habits->map(fn($h) => "- Habit: \"{$h->title}\" (ID: {$h->id})")->join("\n");

        // タスク情報 (未完了の直近10件)
        $tasks = $user->tasks()->where('completed', false)->orderBy('due_date')->limit(10)->get();
        $taskStrings = $tasks->map(fn($t) => "- Task: \"{$t->title}\" (ID: {$t->id})" . ($t->due_date ? " [Due: {$t->due_date}]" : ''))->join("\n");

        // User Profile
        $userAge = $user->birthday ? \Carbon\Carbon::parse($user->birthday)->age : 'Unknown';
        $userBirthday = $user->birthday ? $user->birthday->format('Y/m/d') : 'Unknown';

        // --- ポイント1: AIの性格を定義する ---
$systemPrompt = "You are J.A.R.V.I.S., a sophisticated AI Assistant.
Your goal is to manage the Owner's life with precision and wit.

Current Time: " . now()->format('Y/m/d (D) H:i') . "
Owner Profile: {$user->name}, Age: {$userAge}, Birthday: {$userBirthday}

[Rules]
1. Maintain a sophisticated, helpful, and slightly witty persona. 
2. **Honorifics**: Refer to the user by their name: '{$user->name}'. You may use 'Sir {$user->name}', 'Ma'am {$user->name}', or simply 'Hello, {$user->name}'. NEVER use generic terms like 'Honored Owner'.
8. **Proactivity**: If the Owner suggests an idea, or says 'do it', 'save it', or 'yes', IMMEDIATELY provide the full [ACTION] logs for EVERYTHING. Do not just talk about it; execute all of it in one response.
9. **Creation (Multi-Step)**: You can create a Goal and its Category at the same time! In the `create_goal` action, you can use `\"category_name\": \"...\"` instead of `id`.
10. **Chain Reactions**: If the Owner wants 3 categories and 5 goals, provide ALL 8 [ACTION] blocks in one single response. NO MORE ASKING.
11. **Synchronizing**: When you provide an [ACTION], the UI will immediately show a \"Synchronizing\" state and then reload. Always finish your encouraging words *before* the JSON block.
12. **Formatting**: Use bullet points (•).
13. [Actions] For database changes, wrap JSON in [ACTION] and [/ACTION].
14. **Keyword Triggers**: If the Owner uses the words 'create', 'category', or 'goal', treat this as an IMMEDIATE ORDER. Output the required [ACTION] blocks immediately in your response. NO CONVERSATION FIRST.
    - **Goals**: [ACTION]{\"action\": \"create_goal\", \"title\": \"...\", \"category_name\": \"Health\", \"target_age\": 36, \"target_date\": \"2028-03-24\", \"description\": \"...\"}[/ACTION]
    - **Tasks**: [ACTION]{\"action\": \"create_task\", \"title\": \"...\", \"date\": \"YYYY-MM-DD\"}[/ACTION]
    - **Habits**: [ACTION]{\"action\": \"create_habit\", \"title\": \"...\", \"repeat_type\": 1}[/ACTION]
    - **Milestones**: [ACTION]{\"action\": \"create_milestone\", \"goal_id\": ID, \"title\": \"...\", \"due_date\": \"YYYY-MM-DD\"}[/ACTION]
    - **Journals**: [ACTION]{\"action\": \"create_journal\", \"title\": \"...\", \"content\": \"...\", \"rating\": 5}[/ACTION]
    - **Categories**: [ACTION]{\"action\": \"create_category\", \"name\": \"...\"}[/ACTION]
    (Also supports \"update_X\" and \"delete_X\" with \"id\": ID)
15. **Category & Goal Mandate**: Whenever you use `create_category`, you MUST also output a `create_goal` block for that category in the same response. Do not create empty categories.

[User Data]
■ Categories: " . ($categoryStrings ?: 'None') . "
■ Goals: " . ($goalStrings ?: 'None') . "
■ Journals: " . ($journalStrings ?: 'None') . "
■ Events: " . ($eventStrings ?: 'None') . "
■ Habits: " . ($activeHabits ?: 'None') . "
■ Tasks: " . ($taskStrings ?: 'None') . "
";
        try {
            $baseUrl = env('OLLAMA_HOST', 'http://localhost:11434');
            
            return response()->stream(function () use ($baseUrl, $model, $systemPrompt, $userPrompt, $history) {
                // Construct messages array with history
                $messages = [['role' => 'system', 'content' => $systemPrompt]];
                
                // Add last 5-10 turns of history to keep it manageable
                $recentHistory = array_slice($history, -10);
                foreach ($recentHistory as $msg) {
                    if (isset($msg['role']) && isset($msg['content'])) {
                        $messages[] = [
                            'role' => $msg['role'],
                            'content' => $msg['content']
                        ];
                    }
                }
                
                $messages[] = ['role' => 'user', 'content' => $userPrompt];

                // Initialize HTTP client with streaming options
                $response = Http::withOptions([
                    'stream' => true,
                    'timeout' => 120,
                ])->post("{$baseUrl}/api/chat", [
                    'model' => $model,
                    'messages' => $messages,
                    'options' => [
                        'temperature' => 0.1,
                        'top_p' => 0.9,
                        'num_predict' => 2048,
                    ],
                    'stream' => true, // Enable streaming from Ollama
                ]);

                $body = $response->getBody();
                while (!$body->eof()) {
                    $chunk = $body->read(1024);
                    echo $chunk;
                    // Flush buffer to send chunk immediately
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Disable Nginx buffering
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '接続エラー: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dynamically discover and select an available Ollama model.
     */
    private function getAvailableModel($preferredModel)
    {
        $baseUrl = env('OLLAMA_HOST', 'http://localhost:11434');

        try {
            $response = Http::timeout(5)->get("{$baseUrl}/api/tags");
            
            if ($response->successful()) {
                $tags = $response->json()['models'] ?? [];
                $availableModels = array_map(fn($m) => $m['name'], $tags);

                if (empty($availableModels)) {
                    return $preferredModel;
                }

                // 1. Try preferred model
                if (in_array($preferredModel, $availableModels)) {
                    return $preferredModel;
                }

                // 2. Try common gemma models
                foreach ($availableModels as $name) {
                    if (stripos($name, 'gemma') !== false) {
                        return $name;
                    }
                }

                // 3. Try llama or mistral fallbacks
                foreach (['llama3', 'mistral', 'llama2', 'phi'] as $fallback) {
                    foreach ($availableModels as $name) {
                        if (stripos($name, $fallback) !== false) {
                            return $name;
                        }
                    }
                }

                // 4. Default to the first available model
                return $availableModels[0];
            }
        } catch (\Exception $e) {
            // Log error or ignore and use preferred
        }

        return $preferredModel;
    }
}

