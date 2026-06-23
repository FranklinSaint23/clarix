<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ForumThreadController;
use App\Http\Controllers\ForumPostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController; // ← IMPORTANT
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AiController;

// Contrôleurs instructeur
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\ChapterController as InstructorChapterController;
use App\Http\Controllers\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Instructor\ResourceController as InstructorResourceController;
use App\Http\Controllers\Instructor\QuizController as InstructorQuizController;
use App\Http\Controllers\Instructor\QuestionController as InstructorQuestionController;

// Contrôleur admin
use App\Http\Controllers\Admin\UserController as AdminUserController;

// ─────────────────────────────────────────────────────────
// 1. Routes publiques
// ─────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cours', [CourseController::class, 'index'])->name('courses.index');
Route::get('/cours/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/recherche', [SearchController::class, 'index'])->name('search');
Route::get('/certificat/{enrollment}/verifier', [CertificateController::class, 'verify'])->name('certificate.verify');

// ─────────────────────────────────────────────────────────
// 2. Authentification personnalisée
// ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─────────────────────────────────────────────────────────
// 3. Routes protégées (authentification)
// ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    // Tableaux de bord
    Route::get('/mon-espace', [DashboardController::class, 'student'])->name('dashboard');
    Route::get('/espace-instructeur', [DashboardController::class, 'instructor'])->name('instructor.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');

    // Inscription à un cours
    Route::post('/cours/{course}/inscription', [EnrollmentController::class, 'store'])->name('courses.enroll');

    // Leçons
    Route::get('/lecons/{lesson}', [LessonController::class, 'show'])->name('lessons.show')
        ->middleware('can:view,lesson');
    Route::post('/lecons/{lesson}/completer', [LessonProgressController::class, 'complete'])->name('lessons.complete');

    // Quiz
    Route::get('/quiz/{quiz}/passer', [QuizController::class, 'take'])->name('quizzes.take');
    Route::post('/quiz/{quiz}/soumettre', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quiz/soumission/{submission}', [QuizController::class, 'result'])->name('quizzes.result');
    
    // Forum
    Route::prefix('cours/{course}/forum')->name('courses.forum.')->group(function () {
        Route::get('/', [ForumThreadController::class, 'index'])->name('index');
        Route::get('/creer', [ForumThreadController::class, 'create'])->name('create');
        Route::post('/', [ForumThreadController::class, 'store'])->name('store');
        Route::get('/{thread}', [ForumThreadController::class, 'show'])->name('show');

        Route::post('/{thread}/repondre', [ForumPostController::class, 'store'])->name('posts.store');
        Route::patch('/posts/{post}/solution', [ForumPostController::class, 'markSolution'])->name('posts.solution');
        Route::delete('/posts/{post}', [ForumPostController::class, 'destroy'])->name('posts.destroy');
    });

    // Profil utilisateur (routes supplémentaires)
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Certificat
    Route::get('/certificat/{enrollment}', [CertificateController::class, 'download'])->name('certificate.download');

    // Assistant IA
    Route::prefix('ia')->name('ai.')->group(function () {
        Route::post('/resumer', [AiController::class, 'summarize'])->name('summarize');
        Route::post('/expliquer', [AiController::class, 'explain'])->name('explain');
        Route::post('/questions', [AiController::class, 'questions'])->name('questions');
        Route::post('/chat', [AiController::class, 'chat'])->name('chat');
        Route::post('/quiz/{quiz}/generer', [AiController::class, 'generateQuiz'])->name('quiz.generate');
    });

    //notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
});

// ─────────────────────────────────────────────────────────
// 4. Routes instructeur / admin (gestion des contenus)
// ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,instructor'])
    ->prefix('instructeur')
    ->name('instructor.')
    ->group(function () {
        // Cours
        Route::resource('courses', InstructorCourseController::class);
        // Chapitres
        Route::resource('courses.chapters', InstructorChapterController::class)->except(['index']);
        // Leçons
        Route::resource('chapters.lessons', InstructorLessonController::class)->except(['index']);
        // Ressources
        Route::resource('lessons.resources', InstructorResourceController::class)->except(['index']);
        // Quiz (cours)
        Route::resource('courses.quizzes', InstructorQuizController::class)->except(['index']);
        // Quiz pour une leçon
        Route::get('/lessons/{lesson}/quizzes/create', [InstructorQuizController::class, 'createForLesson'])->name('lessons.quizzes.create');
        Route::post('/lessons/{lesson}/quizzes', [InstructorQuizController::class, 'storeForLesson'])->name('lessons.quizzes.store');
        // Questions d’un quiz
        Route::prefix('quizzes/{quiz}')->group(function () {
            Route::get('/questions/create', [InstructorQuestionController::class, 'create'])->name('questions.create');
            Route::post('/questions', [InstructorQuestionController::class, 'store'])->name('questions.store');
            Route::get('/questions/{question}/edit', [InstructorQuestionController::class, 'edit'])->name('questions.edit');
            Route::put('/questions/{question}', [InstructorQuestionController::class, 'update'])->name('questions.update');
            Route::delete('/questions/{question}', [InstructorQuestionController::class, 'destroy'])->name('questions.destroy');
        });
    });

// ─────────────────────────────────────────────────────────
// 5. Routes administrateur (gestion des utilisateurs)
// ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/utilisateurs', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/utilisateurs/{user}/editer', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/utilisateurs/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/utilisateurs/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });