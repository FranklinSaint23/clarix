<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function student()
    {
        $user = auth()->user();
        $enrollments = $user->enrollments()->with('course.chapters')->latest()->get();
        $totalLessonsCompleted = $user->lessonProgress()->where('completed', true)->count();
        
        // Simulons un streak (vous pouvez le stocker en base)
        $currentStreak = 4;
        
        // Calcul des modules restants (leçons non terminées)
        $pendingModules = 0;
        foreach ($enrollments as $enrollment) {
            $totalLessons = $enrollment->course->lessons()->count();
            $completedLessons = $user->lessonProgress()
                ->where('completed', true)
                ->whereHas('lesson.chapter', fn($q) => $q->where('course_id', $enrollment->course_id))
                ->count();
            $pendingModules += max(0, $totalLessons - $completedLessons);
        }

        return view('dashboard.student', compact('enrollments', 'totalLessonsCompleted', 'currentStreak', 'pendingModules'));
    }

    public function instructor()
    {
        $user = auth()->user();

        // Pagination des cours (10 par page) avec le nombre d'étudiants
        $courses = $user->courses()
            ->withCount('enrollments')
            ->with(['enrollments.user'])
            ->latest()
            ->paginate(10);

        // Pour les totaux globaux, on récupère tous les cours avec le compte d'étudiants
        $allCourses = $user->courses()->withCount('enrollments')->get();

        $totalCourses = $allCourses->count();
        $publishedCourses = $allCourses->where('published', true)->count();
        $totalStudents = $allCourses->sum('enrollments_count'); // ✅ maintenant disponible

        // Revenus totaux
        $totalRevenue = $allCourses->sum(function ($course) {
            return $course->price * $course->enrollments_count;
        });

        // Progression moyenne des étudiants
        $averageProgress = 0;
        $totalProgressSum = 0;
        $totalProgressCount = 0;
        foreach ($allCourses as $course) {
            // On charge les inscriptions seulement si nécessaire (évite N+1)
            $course->loadMissing('enrollments.user');
            foreach ($course->enrollments as $enrollment) {
                $totalProgressSum += $enrollment->progress_percent;
                $totalProgressCount++;
            }
        }
        $averageProgress = $totalProgressCount > 0 ? round($totalProgressSum / $totalProgressCount) : 0;

        // Derniers inscrits (5 derniers)
        $recentEnrollments = \App\Models\Enrollment::whereIn('course_id', $allCourses->pluck('id'))
            ->with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.instructor', compact(
            'courses', 'totalCourses', 'publishedCourses', 'totalStudents',
            'totalRevenue', 'averageProgress', 'recentEnrollments'
        ));
    }
    public function admin()
    {
        $totalUsers       = \App\Models\User::count();
        $totalCourses     = \App\Models\Course::count();
        $publishedCourses = \App\Models\Course::where('published', true)->count();
        $totalEnrollments = \App\Models\Enrollment::count();
        $totalRevenue     = \App\Models\Enrollment::sum('paid_amount');
        $activeStudents   = \App\Models\User::where('role', 'student')->whereHas('enrollments')->count();
        $quizzesPassed    = \App\Models\QuizSubmission::count();

        $avgProgress = 0;
        $allEnrollments = \App\Models\Enrollment::with('course.lessons')->get();
        if ($allEnrollments->count()) {
            $sum = $allEnrollments->sum(fn($e) => $e->progress_percent);
            $avgProgress = round($sum / $allEnrollments->count());
        }

        $monthlyEnrollments = [];
        $monthlyLabels      = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[]      = $date->locale('fr')->isoFormat('MMM YYYY');
            $monthlyEnrollments[] = \App\Models\Enrollment::whereYear('created_at', $date->year)
                                        ->whereMonth('created_at', $date->month)
                                        ->count();
        }

        $usersByRole = [
            \App\Models\User::where('role', 'student')->count(),
            \App\Models\User::where('role', 'instructor')->count(),
            \App\Models\User::where('role', 'admin')->count(),
        ];

        $recentActivities = collect();

        // 5 dernières inscriptions
        $recentActivities = $recentActivities->merge(
            \App\Models\Enrollment::with(['user', 'course'])
                ->latest()->take(5)->get()
                ->map(fn($e) => [
                    'icon'    => 'fa-user-graduate',
                    'color'   => 'green',
                    'text'    => "{$e->user->name} s'est inscrit à « {$e->course->title} »",
                    'time'    => $e->created_at,
                ])
        );

        // 5 derniers utilisateurs créés
        $recentActivities = $recentActivities->merge(
            \App\Models\User::latest()->take(5)->get()
                ->map(fn($u) => [
                    'icon'    => 'fa-user-plus',
                    'color'   => 'blue',
                    'text'    => "Nouveau compte : {$u->name} ({$u->role})",
                    'time'    => $u->created_at,
                ])
        );

        // 5 dernières soumissions de quiz
        $recentActivities = $recentActivities->merge(
            \App\Models\QuizSubmission::with(['user', 'quiz'])
                ->latest()->take(5)->get()
                ->map(fn($s) => [
                    'icon'    => 'fa-brain',
                    'color'   => 'purple',
                    'text'    => "{$s->user->name} a soumis le quiz « {$s->quiz->title} » — {$s->score}%",
                    'time'    => $s->created_at,
                ])
        );

        $recentActivities = $recentActivities->sortByDesc('time')->take(10)->values();

        return view('dashboard.admin', compact(
            'totalUsers', 'totalCourses', 'publishedCourses',
            'totalEnrollments', 'totalRevenue',
            'activeStudents', 'quizzesPassed', 'avgProgress',
            'monthlyLabels', 'monthlyEnrollments', 'usersByRole',
            'recentActivities'
        ));
    }
}