<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Instructeurs ────────────────────────────────────────────────────
        $instructors = [
            ['name' => 'Jean-Baptiste Kouam',  'email' => 'jb@clarix.com'],
            ['name' => 'Sophie Mbarga',         'email' => 'sophie@clarix.com'],
        ];

        $instructorModels = [];
        foreach ($instructors as $i) {
            $instructorModels[] = User::firstOrCreate(['email' => $i['email']], [
                'name'              => $i['name'],
                'password'          => Hash::make('password'),
                'role'              => 'instructor',
                'email_verified_at' => now(),
            ]);
        }

        // ── Étudiants ───────────────────────────────────────────────────────
        $students = [
            ['name' => 'Alice Ngo',       'email' => 'alice@clarix.com'],
            ['name' => 'Bruno Essomba',   'email' => 'bruno@clarix.com'],
            ['name' => 'Chloé Biyong',    'email' => 'chloe@clarix.com'],
            ['name' => 'David Amougou',   'email' => 'david@clarix.com'],
            ['name' => 'Emma Fotso',      'email' => 'emma@clarix.com'],
            ['name' => 'Franck Mbassi',   'email' => 'franck@clarix.com'],
        ];

        $studentModels = [];
        foreach ($students as $s) {
            $studentModels[] = User::firstOrCreate(['email' => $s['email']], [
                'name'              => $s['name'],
                'password'          => Hash::make('password'),
                'role'              => 'student',
                'email_verified_at' => now(),
            ]);
        }

        // ── Cours ───────────────────────────────────────────────────────────
        $coursesData = [
            [
                'instructor' => $instructorModels[0],
                'title'      => 'Laravel 11 — De Zéro à Expert',
                'slug'       => 'laravel-11-de-zero-a-expert',
                'description'=> 'Maîtrisez le framework PHP Laravel 11 de A à Z. Apprenez l\'architecture MVC, Eloquent ORM, les middlewares, les API RESTful et le déploiement en production.',
                'level'      => 'beginner',
                'price'      => 15000,
                'published'  => true,
                'chapters'   => [
                    [
                        'title' => 'Introduction à Laravel',
                        'lessons' => [
                            ['title' => 'Installation & configuration', 'type' => 'text', 'is_free' => true, 'duration' => 15,
                             'content' => '<h2>Installation de Laravel</h2><p>Laravel est un framework PHP élégant qui facilite le développement web. Pour l\'installer, vous avez besoin de PHP 8.2+ et Composer.</p><h3>Étapes</h3><pre><code>composer create-project laravel/laravel mon-projet
cd mon-projet
php artisan serve</code></pre><p>Votre application Laravel est maintenant accessible sur <strong>http://localhost:8000</strong>.</p>'],
                            ['title' => 'Architecture MVC', 'type' => 'text', 'is_free' => true, 'duration' => 20,
                             'content' => '<h2>L\'architecture MVC</h2><p>MVC signifie <strong>Modèle-Vue-Contrôleur</strong>. C\'est un patron de conception qui sépare la logique métier, l\'affichage et le traitement des requêtes.</p><ul><li><strong>Modèle</strong> : gère les données et la base de données</li><li><strong>Vue</strong> : affiche les données à l\'utilisateur</li><li><strong>Contrôleur</strong> : traite les requêtes et coordonne modèle et vue</li></ul>'],
                            ['title' => 'Routes et contrôleurs', 'type' => 'text', 'duration' => 25,
                             'content' => '<h2>Les routes Laravel</h2><p>Les routes définissent les URLs de votre application. Elles se trouvent dans <code>routes/web.php</code>.</p><pre><code>Route::get(\'/bonjour\', function() {
    return \'Bonjour Laravel !\';
});

Route::get(\'/users\', [UserController::class, \'index\']);</code></pre>'],
                        ],
                    ],
                    [
                        'title' => 'Eloquent ORM',
                        'lessons' => [
                            ['title' => 'Modèles et migrations', 'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Les migrations Laravel</h2><p>Les migrations permettent de créer et modifier la structure de votre base de données de façon versionnée.</p><pre><code>php artisan make:model Article -m</code></pre><p>Cela crée un modèle <code>Article</code> et une migration associée.</p>'],
                            ['title' => 'Relations entre modèles', 'type' => 'text', 'duration' => 35,
                             'content' => '<h2>Les relations Eloquent</h2><p>Eloquent supporte plusieurs types de relations :</p><ul><li><code>hasOne</code> / <code>belongsTo</code></li><li><code>hasMany</code> / <code>belongsTo</code></li><li><code>belongsToMany</code> (pivot)</li><li><code>hasManyThrough</code></li></ul>'],
                            ['title' => 'Requêtes avancées', 'type' => 'text', 'duration' => 25,
                             'content' => '<h2>Query Builder & Eloquent avancé</h2><p>Laravel offre un Query Builder expressif :</p><pre><code>User::where(\'role\', \'student\')
    ->whereHas(\'enrollments\')
    ->orderBy(\'name\')
    ->paginate(20);</code></pre>'],
                        ],
                    ],
                    [
                        'title' => 'Authentification & Sécurité',
                        'lessons' => [
                            ['title' => 'Système d\'authentification', 'type' => 'text', 'duration' => 20,
                             'content' => '<h2>Authentification Laravel</h2><p>Laravel fournit un système d\'authentification complet. La façade <code>Auth</code> permet de gérer la connexion, l\'inscription et la déconnexion.</p><pre><code>Auth::attempt([\'email\' => $email, \'password\' => $password]);
Auth::user(); // utilisateur connecté
Auth::logout();</code></pre>'],
                            ['title' => 'Middlewares & Policies', 'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Les Middlewares</h2><p>Un middleware intercepte les requêtes HTTP avant qu\'elles n\'atteignent le contrôleur. Imaginez un gardien qui vérifie votre identité avant de vous laisser entrer.</p><h3>Créer un middleware</h3><pre><code>php artisan make:middleware CheckRole</code></pre><p>Le middleware peut vérifier un rôle, rediriger, ou modifier la requête.</p>'],
                        ],
                    ],
                ],
            ],
            [
                'instructor' => $instructorModels[1],
                'title'      => 'Python pour la Data Science',
                'slug'       => 'python-data-science',
                'description'=> 'Apprenez Python appliqué à la data science : NumPy, Pandas, Matplotlib, Scikit-learn. Passez de l\'analyse de données à la construction de modèles de machine learning.',
                'level'      => 'intermediate',
                'price'      => 20000,
                'published'  => true,
                'chapters'   => [
                    [
                        'title' => 'Python & environnement',
                        'lessons' => [
                            ['title' => 'Installation Python & Jupyter', 'type' => 'text', 'is_free' => true, 'duration' => 10,
                             'content' => '<h2>Configurer votre environnement</h2><p>Pour la data science avec Python, installez Anaconda qui inclut Python, Jupyter Notebook et les principales bibliothèques.</p><pre><code># Créer un environnement virtuel
conda create -n datascience python=3.11
conda activate datascience
pip install numpy pandas matplotlib scikit-learn</code></pre>'],
                            ['title' => 'NumPy — Les tableaux numériques', 'type' => 'text', 'duration' => 40,
                             'content' => '<h2>NumPy</h2><p>NumPy est la bibliothèque fondamentale pour le calcul scientifique en Python. Elle fournit des tableaux multidimensionnels efficaces.</p><pre><code>import numpy as np

arr = np.array([1, 2, 3, 4, 5])
matrix = np.zeros((3, 3))
print(arr.mean(), arr.std())</code></pre>'],
                        ],
                    ],
                    [
                        'title' => 'Analyse de données avec Pandas',
                        'lessons' => [
                            ['title' => 'DataFrames & Series', 'type' => 'text', 'duration' => 45,
                             'content' => '<h2>Pandas DataFrames</h2><p>Un DataFrame est un tableau de données bidimensionnel, comme une feuille Excel mais bien plus puissant.</p><pre><code>import pandas as pd

df = pd.read_csv(\'data.csv\')
print(df.head())
print(df.describe())
df[df[\'age\'] > 25].groupby(\'ville\').mean()</code></pre>'],
                            ['title' => 'Visualisation avec Matplotlib', 'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Visualiser les données</h2><p>Matplotlib est la bibliothèque de visualisation de référence en Python.</p><pre><code>import matplotlib.pyplot as plt

plt.figure(figsize=(10, 6))
plt.plot(df[\'date\'], df[\'ventes\'], color=\'blue\', linewidth=2)
plt.title(\'Évolution des ventes\')
plt.xlabel(\'Date\')
plt.ylabel(\'Ventes\')
plt.show()</code></pre>'],
                        ],
                    ],
                ],
            ],
            [
                'instructor' => $instructorModels[0],
                'title'      => 'HTML & CSS — Créez vos premières pages web',
                'slug'       => 'html-css-premieres-pages-web',
                'description'=> 'Cours complet pour débutants absolus. Apprenez à créer des pages web modernes avec HTML5 et CSS3. Mise en page, Flexbox, Grid, responsive design.',
                'level'      => 'beginner',
                'price'      => 0,
                'published'  => true,
                'chapters'   => [
                    [
                        'title' => 'Les bases de HTML',
                        'lessons' => [
                            ['title' => 'Structure d\'une page HTML', 'type' => 'text', 'is_free' => true, 'duration' => 15,
                             'content' => '<h2>Structure HTML</h2><p>Toute page HTML a la même structure de base :</p><pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="fr"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;title&gt;Ma page&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Bonjour le monde !&lt;/h1&gt;
    &lt;p&gt;Mon premier paragraphe.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>'],
                            ['title' => 'Titres, paragraphes, listes', 'type' => 'text', 'is_free' => true, 'duration' => 20,
                             'content' => '<h2>Les balises de texte</h2><p>HTML fournit des balises pour structurer le contenu :</p><ul><li><code>&lt;h1&gt;</code> à <code>&lt;h6&gt;</code> pour les titres</li><li><code>&lt;p&gt;</code> pour les paragraphes</li><li><code>&lt;ul&gt;</code> / <code>&lt;ol&gt;</code> pour les listes</li><li><code>&lt;strong&gt;</code> pour le gras, <code>&lt;em&gt;</code> pour l\'italique</li></ul>'],
                            ['title' => 'Liens et images', 'type' => 'text', 'duration' => 15,
                             'content' => '<h2>Liens et images en HTML</h2><pre><code>&lt;!-- Lien hypertexte --&gt;
&lt;a href="https://clarix.com"&gt;Visiter CLARIX&lt;/a&gt;

&lt;!-- Image --&gt;
&lt;img src="photo.jpg" alt="Description de l\'image" width="300"&gt;</code></pre>'],
                        ],
                    ],
                    [
                        'title' => 'CSS & Mise en page',
                        'lessons' => [
                            ['title' => 'Sélecteurs & propriétés CSS', 'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Le CSS</h2><p>CSS (Cascading Style Sheets) permet de styliser vos pages HTML.</p><pre><code>/* Sélecteur de balise */
h1 { color: blue; font-size: 2rem; }

/* Sélecteur de classe */
.carte { background: white; border-radius: 8px; padding: 16px; }

/* Sélecteur d\'identifiant */
#header { position: sticky; top: 0; }</code></pre>'],
                            ['title' => 'Flexbox — Mise en page moderne', 'type' => 'text', 'duration' => 35,
                             'content' => '<h2>Flexbox</h2><p>Flexbox est un système de mise en page CSS puissant pour aligner et distribuer les éléments.</p><pre><code>.container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}</code></pre><p>Les propriétés clés : <code>justify-content</code>, <code>align-items</code>, <code>flex-direction</code>, <code>gap</code>.</p>'],
                        ],
                    ],
                ],
            ],
            [
                'instructor' => $instructorModels[1],
                'title'      => 'JavaScript Moderne — ES6 et au-delà',
                'slug'       => 'javascript-moderne-es6',
                'description'=> 'Maîtrisez JavaScript moderne : ES6+, promesses, async/await, fetch API, modules. Construisez des interfaces dynamiques sans framework.',
                'level'      => 'intermediate',
                'price'      => 12000,
                'published'  => true,
                'chapters'   => [
                    [
                        'title' => 'ES6+ Fondamentaux',
                        'lessons' => [
                            ['title' => 'let, const & arrow functions', 'type' => 'text', 'is_free' => true, 'duration' => 20,
                             'content' => '<h2>Variables modernes</h2><p>ES6 introduit <code>let</code> et <code>const</code> qui remplacent <code>var</code> :</p><pre><code>const NOM = "CLARIX"; // immuable
let compteur = 0;     // modifiable

// Arrow function
const doubler = x => x * 2;
const saluer = (prenom) => `Bonjour ${prenom} !`;

// Destructuring
const { nom, age } = utilisateur;
const [premier, ...reste] = tableau;</code></pre>'],
                            ['title' => 'Promesses & Async/Await', 'type' => 'text', 'duration' => 45,
                             'content' => '<h2>Programmation asynchrone</h2><p>JavaScript est asynchrone. Les promesses permettent de gérer les opérations qui prennent du temps (requêtes réseau, lecture de fichiers).</p><pre><code>// Async/Await (syntaxe moderne)
async function chargerDonnees() {
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
                        'title' => 'DOM & Événements',
                        'lessons' => [
                            ['title' => 'Manipulation du DOM', 'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Le DOM</h2><p>Le DOM (Document Object Model) est la représentation de votre page HTML en JavaScript.</p><pre><code>// Sélectionner des éléments
const titre = document.querySelector(\'h1\');
const boutons = document.querySelectorAll(\'.btn\');

// Modifier le contenu
titre.textContent = \'Nouveau titre\';
titre.classList.add(\'actif\');

// Créer un élément
const div = document.createElement(\'div\');
div.innerHTML = \'<p>Nouveau contenu</p>\';
document.body.appendChild(div);</code></pre>'],
                        ],
                    ],
                ],
            ],
        ];

        $createdCourses = [];
        foreach ($coursesData as $courseData) {
            // Reconnexion entre chaque cours pour éviter le timeout TiDB
            \DB::reconnect('mysql');

            $course = Course::firstOrCreate(
                ['slug' => $courseData['slug']],
                [
                    'user_id'     => $courseData['instructor']->id,
                    'title'       => $courseData['title'],
                    'description' => $courseData['description'],
                    'level'       => $courseData['level'],
                    'price'       => $courseData['price'],
                    'published'   => $courseData['published'],
                    'language'    => 'fr',
                ]
            );

            $order = 1;
            foreach ($courseData['chapters'] as $chapterData) {
                $chapter = Chapter::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $chapterData['title']],
                    ['order' => $order++]
                );

                $lessonOrder = 1;
                foreach ($chapterData['lessons'] as $lessonData) {
                    Lesson::firstOrCreate(
                        ['chapter_id' => $chapter->id, 'title' => $lessonData['title']],
                        [
                            'type'             => $lessonData['type'],
                            'content'          => $lessonData['content'] ?? null,
                            'duration_minutes' => $lessonData['duration'] ?? 0,
                            'is_free'          => $lessonData['is_free'] ?? false,
                            'order'            => $lessonOrder++,
                        ]
                    );
                }
            }

            $createdCourses[] = $course;
        }

        // ── Inscriptions & progression ──────────────────────────────────────
        $enrollmentData = [
            // Alice inscrite dans Laravel et HTML/CSS (HTML terminé)
            [$studentModels[0], $createdCourses[0], 'active',    null],
            [$studentModels[0], $createdCourses[2], 'completed', now()->subDays(5)],
            // Bruno inscrit dans tous les cours
            [$studentModels[1], $createdCourses[0], 'active',    null],
            [$studentModels[1], $createdCourses[1], 'active',    null],
            [$studentModels[1], $createdCourses[3], 'active',    null],
            // Chloé inscrite dans Python et JS
            [$studentModels[2], $createdCourses[1], 'active',    null],
            [$studentModels[2], $createdCourses[3], 'active',    null],
            // David dans Laravel
            [$studentModels[3], $createdCourses[0], 'active',    null],
            [$studentModels[3], $createdCourses[2], 'completed', now()->subDays(10)],
            // Emma dans HTML/CSS et Python
            [$studentModels[4], $createdCourses[2], 'completed', now()->subDays(3)],
            [$studentModels[4], $createdCourses[1], 'active',    null],
            // Franck dans JS
            [$studentModels[5], $createdCourses[3], 'active',    null],
        ];

        foreach ($enrollmentData as [$student, $course, $status, $completedAt]) {
            Enrollment::firstOrCreate(
                ['user_id' => $student->id, 'course_id' => $course->id],
                [
                    'status'       => $status,
                    'completed_at' => $completedAt,
                    'paid_amount'  => $course->price,
                ]
            );
        }

        // ── Progression de leçons pour quelques étudiants ──────────────────
        // Alice a complété toutes les leçons du cours HTML/CSS
        $htmlCourse = $createdCourses[2];
        foreach ($htmlCourse->lessons as $lesson) {
            LessonProgress::firstOrCreate(
                ['user_id' => $studentModels[0]->id, 'lesson_id' => $lesson->id],
                ['completed' => true, 'completed_at' => now()->subDays(6)]
            );
        }

        // Bruno a fait les 2 premières leçons de Laravel
        $laravelLessons = $createdCourses[0]->lessons->take(2);
        foreach ($laravelLessons as $lesson) {
            LessonProgress::firstOrCreate(
                ['user_id' => $studentModels[1]->id, 'lesson_id' => $lesson->id],
                ['completed' => true, 'completed_at' => now()->subDays(2)]
            );
        }

        $this->command->info('✅ Données de démo créées :');
        $this->command->table(
            ['Type', 'Quantité'],
            [
                ['Instructeurs', count($instructors)],
                ['Étudiants',    count($students)],
                ['Cours',        count($coursesData)],
                ['Inscriptions', count($enrollmentData)],
            ]
        );
    }
}
