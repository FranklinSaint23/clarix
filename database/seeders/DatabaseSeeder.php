<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Enrollment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Comptes utilisateurs ────────────────────────────────────────
        $admin = User::create([
            'name'              => 'Administrateur',
            'email'             => 'admin@elearning.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        $instructor = User::create([
            'name'              => 'Jean Instructeur',
            'email'             => 'instructor@elearning.com',
            'password'          => Hash::make('password'),
            'role'              => 'instructor',
            'bio'               => 'Développeur web passionné avec 10 ans d\'expérience.',
            'email_verified_at' => now(),
        ]);

        $student = User::create([
            'name'              => 'Marie Apprenante',
            'email'             => 'student@elearning.com',
            'password'          => Hash::make('password'),
            'role'              => 'student',
            'email_verified_at' => now(),
        ]);

        // ── 2. Cours exemple ───────────────────────────────────────────────
        $course = Course::create([
            'user_id'          => $instructor->id,
            'title'            => 'Laravel 11 — De débutant à expert',
            'slug'             => 'laravel-11-debutant-expert',
            'description'      => 'Apprenez Laravel 11 de A à Z : routes, Eloquent, Blade, API REST et déploiement.',
            'level'            => 'beginner',
            'language'         => 'fr',
            'price'            => 0,
            'published'        => true,
            'duration_minutes' => 480,
        ]);

        // ── 3. Chapitres ───────────────────────────────────────────────────
        $chapter1 = Chapter::create([
            'course_id'   => $course->id,
            'title'       => 'Introduction à Laravel',
            'description' => 'Prise en main de l\'environnement et des concepts de base.',
            'order'       => 1,
        ]);

        $chapter2 = Chapter::create([
            'course_id'   => $course->id,
            'title'       => 'Bases de données & Eloquent',
            'description' => 'Migrations, modèles et relations Eloquent.',
            'order'       => 2,
        ]);

        // ── 4. Leçons ──────────────────────────────────────────────────────
        $lesson1 = Lesson::create([
            'chapter_id'       => $chapter1->id,
            'title'            => 'Installation et configuration',
            'type'             => 'video',
            'video_url'        => 'https://www.youtube.com/watch?v=example',
            'duration_minutes' => 15,
            'is_free'          => true,
            'order'            => 1,
        ]);

        $lesson2 = Lesson::create([
            'chapter_id'       => $chapter1->id,
            'title'            => 'Structure d\'un projet Laravel',
            'type'             => 'video',
            'duration_minutes' => 20,
            'is_free'          => true,
            'order'            => 2,
        ]);

        $lesson3 = Lesson::create([
            'chapter_id'       => $chapter2->id,
            'title'            => 'Migrations et modèles',
            'type'             => 'video',
            'duration_minutes' => 30,
            'is_free'          => false,
            'order'            => 1,
        ]);

        // ── 5. Quiz exemple ────────────────────────────────────────────────
        $quiz = Quiz::create([
            'course_id'    => $course->id,
            'lesson_id'    => $lesson2->id,
            'title'        => 'Quiz — Structure Laravel',
            'description'  => 'Testez vos connaissances sur la structure d\'un projet Laravel.',
            'pass_score'   => 60,
            'time_limit'   => 10,
            'max_attempts' => 3,
            'show_answers' => true,
        ]);

        $q1 = Question::create([
            'quiz_id'       => $quiz->id,
            'question_text' => 'Quel dossier contient les contrôleurs dans Laravel ?',
            'type'          => 'single',
            'points'        => 1,
            'explanation'   => 'Les contrôleurs sont dans app/Http/Controllers.',
            'order'         => 1,
        ]);
        Answer::insert([
            ['question_id' => $q1->id, 'answer_text' => 'app/Http/Controllers', 'is_correct' => true,  'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q1->id, 'answer_text' => 'resources/controllers', 'is_correct' => false, 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q1->id, 'answer_text' => 'routes/controllers',    'is_correct' => false, 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q1->id, 'answer_text' => 'src/Controllers',       'is_correct' => false, 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $q2 = Question::create([
            'quiz_id'       => $quiz->id,
            'question_text' => 'Quel fichier définit les routes web dans Laravel ?',
            'type'          => 'single',
            'points'        => 1,
            'explanation'   => 'Le fichier routes/web.php gère les routes HTTP.',
            'order'         => 2,
        ]);
        Answer::insert([
            ['question_id' => $q2->id, 'answer_text' => 'routes/web.php',      'is_correct' => true,  'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q2->id, 'answer_text' => 'app/routes.php',      'is_correct' => false, 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q2->id, 'answer_text' => 'config/routes.php',   'is_correct' => false, 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q2->id, 'answer_text' => 'bootstrap/routes.php','is_correct' => false, 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 6. Inscription de l'apprenant au cours ─────────────────────────
        Enrollment::create([
            'user_id'   => $student->id,
            'course_id' => $course->id,
            'status'    => 'active',
        ]);

        // ── Résumé console ─────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->table(
            ['Email', 'Mot de passe', 'Rôle'],
            [
                ['admin@elearning.com',      'password', 'admin'],
                ['instructor@elearning.com', 'password', 'instructor'],
                ['student@elearning.com',    'password', 'student'],
            ]
        );
        $this->command->info('📚 1 cours · 2 chapitres · 3 leçons · 1 quiz · 2 questions créés.');
    }
}
