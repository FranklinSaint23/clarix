<?php

namespace App\Console\Commands;

use App\Models\Answer;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedAdminUser extends Command
{
    protected $signature = 'admin:seed';
    protected $description = 'Crée les données de base (admin, instructeurs, étudiants, cours) si elles n\'existent pas';

    public function handle(): void
    {
        $this->seedAdmin();
        $this->seedInstructors();
        $this->seedStudents();
        $this->seedCourses();
        $this->info('✅ Données CLARIX prêtes.');
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedAdmin(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@clarix.com'],
            [
                'name'              => 'Administrateur',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedInstructors(): array
    {
        $list = [
            ['name' => 'Jean-Baptiste Kouam', 'email' => 'jb@clarix.com'],
            ['name' => 'Sophie Mbarga',        'email' => 'sophie@clarix.com'],
        ];
        $models = [];
        foreach ($list as $data) {
            $models[] = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password'), 'role' => 'instructor', 'email_verified_at' => now()]
            );
        }
        return $models;
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedStudents(): array
    {
        $list = [
            ['name' => 'Alice Ngo',     'email' => 'alice@clarix.com'],
            ['name' => 'Bruno Essomba', 'email' => 'bruno@clarix.com'],
            ['name' => 'Chloé Biyong',  'email' => 'chloe@clarix.com'],
            ['name' => 'David Amougou', 'email' => 'david@clarix.com'],
            ['name' => 'Emma Fotso',    'email' => 'emma@clarix.com'],
            ['name' => 'Franck Mbassi', 'email' => 'franck@clarix.com'],
        ];
        $models = [];
        foreach ($list as $data) {
            $models[] = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password'), 'role' => 'student', 'email_verified_at' => now()]
            );
        }
        return $models;
    }

    // ────────────────────────────────────────────────────────────────────────
    private function seedCourses(): void
    {
        $instructors = $this->seedInstructors();
        $students    = $this->seedStudents();

        $coursesData = [
            [
                'instructor' => $instructors[0],
                'title'      => 'Laravel 11 — De Zéro à Expert',
                'slug'       => 'laravel-11-de-zero-a-expert',
                'description'=> 'Maîtrisez Laravel 11 de A à Z : MVC, Eloquent ORM, middlewares, API RESTful et déploiement en production.',
                'level'      => 'beginner',
                'price'      => 15000,
                'chapters'   => [
                    [
                        'title'   => 'Introduction à Laravel',
                        'lessons' => [
                            ['title' => 'Installation & configuration', 'is_free' => true, 'duration' => 15,
                             'content' => '<h2>Installation de Laravel</h2><p>Laravel est un framework PHP élégant. Pour l\'installer, vous avez besoin de PHP 8.2+ et Composer.</p><pre><code>composer create-project laravel/laravel mon-projet
cd mon-projet
php artisan serve</code></pre><p>Votre application est accessible sur <strong>http://localhost:8000</strong>.</p>'],
                            ['title' => 'Architecture MVC', 'is_free' => true, 'duration' => 20,
                             'content' => '<h2>L\'architecture MVC</h2><p>MVC signifie <strong>Modèle-Vue-Contrôleur</strong>. C\'est un patron de conception qui sépare la logique métier, l\'affichage et le traitement des requêtes.</p><ul><li><strong>Modèle</strong> : gère les données</li><li><strong>Vue</strong> : affiche les données</li><li><strong>Contrôleur</strong> : traite les requêtes</li></ul>'],
                            ['title' => 'Routes et contrôleurs', 'duration' => 25,
                             'content' => '<h2>Les routes Laravel</h2><p>Les routes définissent les URLs de votre application dans <code>routes/web.php</code>.</p><pre><code>Route::get(\'/bonjour\', fn() => \'Bonjour !\');
Route::resource(\'cours\', CourseController::class);</code></pre>'],
                        ],
                    ],
                    [
                        'title'   => 'Eloquent ORM',
                        'lessons' => [
                            ['title' => 'Modèles et migrations', 'duration' => 30,
                             'content' => '<h2>Les migrations</h2><p>Les migrations permettent de créer et modifier la structure de votre base de données de façon versionnée.</p><pre><code>php artisan make:model Article -m
php artisan migrate</code></pre>'],
                            ['title' => 'Relations entre modèles', 'duration' => 35,
                             'content' => '<h2>Les relations Eloquent</h2><ul><li><code>hasOne</code> / <code>belongsTo</code></li><li><code>hasMany</code></li><li><code>belongsToMany</code> (pivot)</li><li><code>hasManyThrough</code></li></ul>'],
                            ['title' => 'Requêtes avancées', 'duration' => 25,
                             'content' => '<h2>Query Builder avancé</h2><pre><code>User::where(\'role\', \'student\')
    ->whereHas(\'enrollments\')
    ->orderBy(\'name\')
    ->paginate(20);</code></pre>'],
                        ],
                    ],
                    [
                        'title'   => 'Authentification & Sécurité',
                        'lessons' => [
                            ['title' => 'Système d\'authentification', 'duration' => 20,
                             'content' => '<h2>Authentification Laravel</h2><pre><code>Auth::attempt([\'email\' => $email, \'password\' => $password]);
Auth::user();
Auth::logout();</code></pre>'],
                            ['title' => 'Middlewares & Policies', 'duration' => 30,
                             'content' => '<h2>Les Middlewares</h2><p>Un middleware intercepte les requêtes HTTP. Imaginez un gardien qui vérifie votre identité avant de vous laisser entrer.</p><pre><code>php artisan make:middleware CheckRole</code></pre>'],
                        ],
                    ],
                ],
                'enrollments' => [$students[0], $students[1], $students[3]],
                'completed_by' => [],
            ],
            [
                'instructor' => $instructors[1],
                'title'      => 'Python pour la Data Science',
                'slug'       => 'python-data-science',
                'description'=> 'Python appliqué à la data science : NumPy, Pandas, Matplotlib, Scikit-learn. De l\'analyse à la construction de modèles de machine learning.',
                'level'      => 'intermediate',
                'price'      => 20000,
                'chapters'   => [
                    [
                        'title'   => 'Python & environnement',
                        'lessons' => [
                            ['title' => 'Installation Python & Jupyter', 'is_free' => true, 'duration' => 10,
                             'content' => '<h2>Configurer l\'environnement</h2><p>Installez Anaconda qui inclut Python, Jupyter et les bibliothèques principales.</p><pre><code>conda create -n datascience python=3.11
conda activate datascience
pip install numpy pandas matplotlib scikit-learn</code></pre>'],
                            ['title' => 'NumPy — Les tableaux numériques', 'duration' => 40,
                             'content' => '<h2>NumPy</h2><p>NumPy est la bibliothèque fondamentale pour le calcul scientifique.</p><pre><code>import numpy as np
arr = np.array([1, 2, 3, 4, 5])
print(arr.mean(), arr.std())</code></pre>'],
                        ],
                    ],
                    [
                        'title'   => 'Analyse de données avec Pandas',
                        'lessons' => [
                            ['title' => 'DataFrames & Series', 'duration' => 45,
                             'content' => '<h2>Pandas DataFrames</h2><p>Un DataFrame est comme une feuille Excel, mais bien plus puissant.</p><pre><code>import pandas as pd
df = pd.read_csv(\'data.csv\')
df[df[\'age\'] > 25].groupby(\'ville\').mean()</code></pre>'],
                            ['title' => 'Visualisation avec Matplotlib', 'duration' => 30,
                             'content' => '<h2>Visualiser les données</h2><pre><code>import matplotlib.pyplot as plt
plt.plot(df[\'date\'], df[\'ventes\'], color=\'blue\')
plt.title(\'Évolution des ventes\')
plt.show()</code></pre>'],
                        ],
                    ],
                ],
                'enrollments' => [$students[1], $students[2], $students[4]],
                'completed_by' => [],
            ],
            [
                'instructor' => $instructors[0],
                'title'      => 'HTML & CSS — Créez vos premières pages web',
                'slug'       => 'html-css-premieres-pages-web',
                'description'=> 'Cours pour débutants absolus. Créez des pages web modernes avec HTML5 et CSS3 : Flexbox, Grid, responsive design.',
                'level'      => 'beginner',
                'price'      => 0,
                'chapters'   => [
                    [
                        'title'   => 'Les bases de HTML',
                        'lessons' => [
                            ['title' => 'Structure d\'une page HTML', 'is_free' => true, 'duration' => 15,
                             'content' => '<h2>Structure HTML</h2><pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="fr"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;title&gt;Ma page&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Bonjour !&lt;/h1&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>'],
                            ['title' => 'Titres, paragraphes, listes', 'is_free' => true, 'duration' => 20,
                             'content' => '<h2>Balises de texte</h2><ul><li><code>&lt;h1&gt;</code> à <code>&lt;h6&gt;</code> pour les titres</li><li><code>&lt;p&gt;</code> pour les paragraphes</li><li><code>&lt;ul&gt;</code> / <code>&lt;ol&gt;</code> pour les listes</li><li><code>&lt;strong&gt;</code> pour le gras</li></ul>'],
                            ['title' => 'Liens et images', 'duration' => 15,
                             'content' => '<h2>Liens et images</h2><pre><code>&lt;a href="https://clarix.com"&gt;Visiter CLARIX&lt;/a&gt;
&lt;img src="photo.jpg" alt="Description" width="300"&gt;</code></pre>'],
                        ],
                    ],
                    [
                        'title'   => 'CSS & Mise en page',
                        'lessons' => [
                            ['title' => 'Sélecteurs & propriétés CSS', 'duration' => 30,
                             'content' => '<h2>Le CSS</h2><pre><code>h1 { color: blue; font-size: 2rem; }
.carte { background: white; border-radius: 8px; padding: 16px; }
#header { position: sticky; top: 0; }</code></pre>'],
                            ['title' => 'Flexbox — Mise en page moderne', 'duration' => 35,
                             'content' => '<h2>Flexbox</h2><pre><code>.container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}</code></pre>'],
                        ],
                    ],
                ],
                'enrollments' => [$students[0], $students[1], $students[3], $students[4]],
                'completed_by' => [$students[0], $students[3], $students[4]],
            ],
            [
                'instructor' => $instructors[1],
                'title'      => 'JavaScript Moderne — ES6 et au-delà',
                'slug'       => 'javascript-moderne-es6',
                'description'=> 'Maîtrisez JavaScript moderne : ES6+, promesses, async/await, fetch API, manipulation du DOM.',
                'level'      => 'intermediate',
                'price'      => 12000,
                'chapters'   => [
                    [
                        'title'   => 'ES6+ Fondamentaux',
                        'lessons' => [
                            ['title' => 'let, const & arrow functions', 'is_free' => true, 'duration' => 20,
                             'content' => '<h2>Variables modernes</h2><pre><code>const NOM = "CLARIX";
let compteur = 0;

const doubler = x => x * 2;
const saluer = (prenom) => `Bonjour ${prenom} !`;

const { nom, age } = utilisateur;
const [premier, ...reste] = tableau;</code></pre>'],
                            ['title' => 'Promesses & Async/Await', 'duration' => 45,
                             'content' => '<h2>Programmation asynchrone</h2><pre><code>async function chargerDonnees() {
    try {
        const reponse = await fetch(\'/api/cours\');
        const donnees = await reponse.json();
        return donnees;
    } catch (erreur) {
        console.error(\'Erreur :\', erreur);
    }
}</code></pre>'],
                        ],
                    ],
                    [
                        'title'   => 'DOM & Événements',
                        'lessons' => [
                            ['title' => 'Manipulation du DOM', 'duration' => 30,
                             'content' => '<h2>Le DOM</h2><pre><code>const titre = document.querySelector(\'h1\');
titre.textContent = \'Nouveau titre\';
titre.classList.add(\'actif\');

const div = document.createElement(\'div\');
div.innerHTML = \'&lt;p&gt;Nouveau contenu&lt;/p&gt;\';
document.body.appendChild(div);</code></pre>'],
                        ],
                    ],
                ],
                'enrollments' => [$students[2], $students[5]],
                'completed_by' => [],
            ],
        ];

        foreach ($coursesData as $data) {
            $course = Course::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'user_id'     => $data['instructor']->id,
                    'title'       => $data['title'],
                    'description' => $data['description'],
                    'level'       => $data['level'],
                    'price'       => $data['price'],
                    'published'   => true,
                    'language'    => 'fr',
                ]
            );

            // Chapitres & leçons
            $chapterOrder = 1;
            foreach ($data['chapters'] as $chapterData) {
                $chapter = Chapter::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $chapterData['title']],
                    ['order' => $chapterOrder++]
                );
                $lessonOrder = 1;
                foreach ($chapterData['lessons'] as $lessonData) {
                    Lesson::firstOrCreate(
                        ['chapter_id' => $chapter->id, 'title' => $lessonData['title']],
                        [
                            'type'             => 'text',
                            'content'          => $lessonData['content'],
                            'duration_minutes' => $lessonData['duration'] ?? 0,
                            'is_free'          => $lessonData['is_free'] ?? false,
                            'order'            => $lessonOrder++,
                        ]
                    );
                }
            }

            // Inscriptions
            foreach ($data['enrollments'] as $student) {
                $isCompleted = in_array($student, $data['completed_by']);
                Enrollment::firstOrCreate(
                    ['user_id' => $student->id, 'course_id' => $course->id],
                    [
                        'status'       => $isCompleted ? 'completed' : 'active',
                        'completed_at' => $isCompleted ? now()->subDays(rand(3, 15)) : null,
                        'paid_amount'  => $course->price,
                    ]
                );
            }

            // Progression : les étudiants "completed" ont toutes les leçons terminées
            foreach ($data['completed_by'] as $student) {
                foreach ($course->lessons as $lesson) {
                    LessonProgress::firstOrCreate(
                        ['user_id' => $student->id, 'lesson_id' => $lesson->id],
                        ['completed' => true, 'completed_at' => now()->subDays(rand(3, 20))]
                    );
                }
            }
        }
    }
}
