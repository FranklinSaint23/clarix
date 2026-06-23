<?php

namespace App\Notifications;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLessonNotification extends Notification
{
    use Queueable;

    protected $lesson;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Une nouvelle leçon '{$this->lesson->title}' a été ajoutée au cours '{$this->lesson->chapter->course->title}'.",
            'lesson_id' => $this->lesson->id,
            'course_id' => $this->lesson->chapter->course_id,
        ];
    }
}