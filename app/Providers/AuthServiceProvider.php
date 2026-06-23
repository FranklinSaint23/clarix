<?php

namespace App\Providers;

use App\Models\Lesson;
use App\Policies\LessonPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les mappings des politiques pour les modèles.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Lesson::class => LessonPolicy::class,
        DatabaseNotification::class => \App\Policies\NotificationPolicy::class,
    ];

    /**
     * Enregistrer les services d'authentification / autorisation.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}