<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    public function update(User $user, DatabaseNotification $notification)
    {
        return $user->id === $notification->notifiable_id;
    }
    public function delete(User $user, DatabaseNotification $notification)
    {
        return $user->id === $notification->notifiable_id;
    }
}