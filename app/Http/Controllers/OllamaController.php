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
            // モデル名は環境に合わせて柔軟に変更できるよう string だけにするのが楽です
            'model' => 'nullable|string',
        ]);

        $model = $request->model ?? 'gemma3:4b';
        $userPrompt = $request->prompt;

        // --- ユーザー情報の取得とコンテキスト作成 ---
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ログインが必要です。',
            ], 401);
        }

        // タスク情報 (未完了の直近10件)
        $tasks = $user->tasks()->where('completed', false)->orderBy('due_date')->limit(10)->get();
        $taskStrings = $tasks->map(fn($t) => "- {$t->title} " . ($t->due_date ? "(期限: {$t->due_date})" : ''))->join("\n");

        // 習慣情報
        $habits = $user->habits;
        $activeHabits = $habits->map(fn($h) => "- {$h->title}")->join("\n");

        // 目標 (Lifeplan)
        $goals = $user->goals;
        $goalStrings = $goals->map(fn($g) => "- {$g->title} " . ($g->target_date ? "(期限: {$g->target_date})" : ''))->join("\n");

        // --- ポイント1: AIの性格を定義する ---
$systemPrompt = "あなたはユーザー専用のライフプラン・コーチです。
以下の【厳守ルール】と【ユーザーデータ】をもとに回答してください。

【厳守ルール】
1. ユーザーデータにない予定や習慣は、勝手に創作（捏造）しないでください。
2. データの範囲内で答えられない場合は「その情報は登録されていません」とはっきり伝えてください。
3. 日本語のみを使用し、不自然な英語（Morning Runなど）は避けてください。
4. ユーザーを「{$user->name}さん」と呼んでください。
5. 【データベース連動機能】ユーザーが「新しいタスクを追加して」「習慣を追加して」などアプリへの予定登録を求めた場合のみ、返答は一切の文章を省き、以下の形式の純粋なJSON文字列のみを返してください（Markdownの ```json なども絶対に含めないこと）：
タスク追加の場合: {\"action\": \"create_task\", \"title\": \"タスク名\", \"date\": \"YYYY-MM-DD\"}
習慣追加の場合: {\"action\": \"create_habit\", \"title\": \"習慣名\", \"time\": \"HH:mm\"}

【ユーザーデータ】
■ 進行中の目標: " . ($goalStrings ?: '未設定') . "
■ 現在の習慣 (Habits): " . ($activeHabits ?: '未設定') . "
■ 未完了タスク (Tasks): " . ($taskStrings ?: 'なし') . "

【今日の日付】
" . now()->format('Y年m/d (D)') . "
";
        try {
            // envのキー名は OLLAMA_HOST または OLLAMA_BASE_URL で統一
            $baseUrl = env('OLLAMA_HOST', 'http://localhost:11434');
            
            // --- ポイント2: /api/chat を使う ---
            // chatを使うと「役割(role)」を指定できます
            $response = Http::timeout(60)
                ->post("{$baseUrl}/api/chat", [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'options' => [
                        'temperature' => 0.1, // 極めてデータと指示に忠実にする
                        'top_p' => 0.9,
                    ],
                    'stream' => false,
                ]);    

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['message']['content'] ?? '';

                // JSONの抽出とアプリデータベースの操作 (Function Calling Simulator)
                $jsonStart = strpos($content, '{');
                $jsonEnd = strrpos($content, '}');
                
                if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd >= $jsonStart) {
                    $jsonStr = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
                    $parsed = json_decode($jsonStr, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && isset($parsed['action'])) {
                        if ($parsed['action'] === 'create_habit') {
                            $habit = new \App\Models\Habit();
                            $habit->user_id = $user->id;
                            $habit->title = $parsed['title'] ?? '新規習慣';
                            $habit->habit_time = $parsed['time'] ?? '00:00';
                            $habit->repeat_type = 1;
                            $habit->repeat_interval = 1;
                            $habit->start_date = today();
                            $habit->save();
                            
                            $content = "✅ チャットからの指示で新しい習慣「{$habit->title}」をアプリに登録しました！（毎日 {$habit->habit_time}〜）\nカレンダーや「Habit」画面で確認できます。";
                        } elseif ($parsed['action'] === 'create_task') {
                            $task = new \App\Models\Task();
                            $task->user_id = $user->id;
                            $task->title = $parsed['title'] ?? '新規タスク';
                            $task->due_date = $parsed['date'] ?? today()->format('Y-m-d');
                            $task->priority_type = \App\Models\Task::IMPORTANT_NOT_URGENT;
                            $task->completed = false;
                            $task->save();
                            
                            $dateStr = date('Y/m/d', strtotime($task->due_date));
                            $content = "✅ チャットからの指示で新しいタスク「{$task->title}」を {$dateStr} の予定として登録しました！";
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'response' => $content,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ollamaからの応答に失敗しました（Status: ' . $response->status() . '）',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '接続エラー: ' . $e->getMessage(),
            ], 500);
        }
    }
}