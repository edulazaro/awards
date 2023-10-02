<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\LessonUser;
use App\Models\User;


class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];

    /**
     * Belongs to many lesson users.
     *
     * @return BelongsToMany
     */
    public function userLessons(): BelongsToMany
    {
        return $this->belongsToMany(LessonUser::class, 'lesson_user');
    }

}
