<?php
// app/Models/Resource.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resource extends Model
{
    use HasFactory;
    protected $fillable = ['lesson_id', 'title', 'file_path', 'file_type', 'file_size'];

    public function lesson() { return $this->belongsTo(Lesson::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $kb = $this->file_size / 1024;
        return $kb >= 1024
            ? number_format($kb / 1024, 1) . ' Mo'
            : number_format($kb, 0) . ' Ko';
    }
}
