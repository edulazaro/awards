<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Concerns\HasRewards;

use App\Models\Lesson;
use App\Models\Comment;
use App\Models\LessonUser;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRewards;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get user watched lessons.
     *
     * @return HasManyThrough
     */
    /*
    public function watched(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lesson::class,
            LessonUser::class,
        )->where('watched', 1);
    }
    */
    public function watched(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user', 'user_id', 'lesson_id')->wherePivot('watched', 1);
    }

    /**
     * Get user comments.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
