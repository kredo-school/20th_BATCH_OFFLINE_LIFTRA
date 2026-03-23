<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Api\HabitController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Profile\EducationController;
use App\Http\Controllers\Api\Profile\ExperienceController;
use App\Http\Controllers\Api\Profile\CertificationController;
use App\Http\Controllers\Api\Profile\SkillController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LifeplanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



// Laravel 認証ルート
Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('login');

// Google Login
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');

// ログイン後ホーム
Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/help', [SettingsController::class, 'help'])->name('settings.help');

    // プロフィール
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');      // プロフィール表示
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');    // 編集フォーム
        Route::put('/update', [ProfileController::class, 'update'])->name('update'); // 更新処理

        // ------------------------------------
        // Education
        Route::post('/education/store', [EducationController::class, 'store'])->name('education.store');
        Route::put('/education/{id}/update', [EducationController::class, 'update'])->name('education.update');
        Route::delete('/education/{id}/delete', [EducationController::class, 'destroy'])->name('education.destroy');

        // Experience
        Route::post('/experience/store', [ExperienceController::class, 'store'])->name('experience.store');
        Route::put('/experience/{id}/update', [ExperienceController::class, 'update'])->name('experience.update');
        Route::delete('/experience/{id}/delete', [ExperienceController::class, 'destroy'])->name('experience.destroy');

        // Certification
        Route::post('/certification/store', [CertificationController::class, 'store'])->name('certification.store');
        Route::put('/certification/{id}/update', [CertificationController::class, 'update'])->name('certification.update');
        Route::delete('/certification/{id}/delete', [CertificationController::class, 'destroy'])->name('certification.destroy');

        // Skill
        Route::post('/skill/store', [SkillController::class, 'store'])->name('skill.store');
        Route::put('/skill/{id}/update', [SkillController::class, 'update'])->name('skill.update');
        Route::delete('/skill/{id}/delete', [SkillController::class, 'destroy'])->name('skill.destroy');
    });

    // Task　https://chatgpt.com/share/69ae87ee-7548-8003-b4ea-cac8213172ae
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::patch('/{task}/complete', [TaskController::class, 'complete'])->name('complete');
        Route::post('/store', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::patch('/{task}/update', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}/destroy', [TaskController::class, 'destroy'])->name('destroy');
    });

    // Habit
    Route::prefix('habits')->name('habits.')->group(function () {
        Route::get('/', [HabitController::class, 'index'])->name('index');
        Route::get('/day',[HabitController::class,'getHabitsByDate'])->name('byDate');
        Route::post('/store', [HabitController::class, 'store'])->name('store');
        Route::put('/{habit}/update', [HabitController::class, 'update'])->name('update');
        Route::delete('/{habit}/delete', [HabitController::class, 'destroy'])->name('destroy');
        Route::post('/{habit}/toggle', [HabitController::class, 'toggle'])->name('toggle');
    });

    // Journal
    Route::prefix('journals')->name('journals.')->group(function () {
        Route::get('/', [JournalController::class, 'index'])->name('index');
        Route::post('/store', [JournalController::class, 'store'])->name('store');
        Route::get('/{journal}/edit', [JournalController::class, 'edit'])->name('edit');
        Route::put('/{journal}/update', [JournalController::class, 'update'])->name('update');
        Route::delete('/{journal}/delete', [JournalController::class, 'destroy'])->name('destroy');
    });

    // Lifeplan
    Route::prefix('lifeplan')->name('lifeplan.')->group(function () {
        Route::post('/category/store', [LifeplanController::class, 'storeCategory'])->name('category.store');
        Route::get('/category/{category}', [LifeplanController::class, 'showCategory'])->name('category.show');
        Route::put('/category/{category}', [LifeplanController::class, 'updateCategory'])->name('category.update');
        Route::delete('/category/{category}', [LifeplanController::class, 'destroyCategory'])->name('category.destroy');
        Route::post('/goal/store', [LifeplanController::class, 'storeGoal'])->name('goal.store');
        Route::get('/goal/{goal}', [LifeplanController::class, 'showGoal'])->name('goal.show');
        Route::post('/milestone/store', [LifeplanController::class, 'storeMilestone'])->name('milestone.store');
    });

    // Ollama AI Assistant
    Route::post('/api/ollama/generate', [\App\Http\Controllers\OllamaController::class, 'generate'])->name('ollama.generate');
});