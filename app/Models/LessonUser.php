<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

use App\Models\Lesson;

class LessonUser extends Model
{
    use HasFactory;

    protected $table = 'lesson_user';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_id',
        'user_id',
        'watched'
    ];

    /**
     * Get lesson.
     *
     * @return BelongsTo
     */
    public function lesson(): BelongsTo
    {
        return $this->BelongsTo(Lesson::class);
    }

    /**
     * Get user.-
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
