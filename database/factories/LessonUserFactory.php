<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonUser;

class LessonUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition($user_id = false)
    {
        $query = Lesson::query();

        return [
            'lesson_id' => $this->faker->randomElement(),
            'watched' => 1
        ];
    }


    public function withUser($userId): Factory
    {
        $user = User::findOrFail($userId);

        $watchedLessonIds = $user->watched->pluck('id');
        $lessonIds = Lesson::whereNotIn('id', $watchedLessonIds)->get()->pluck('id');

        return $this->state([
            'user_id' => $user->id,
            'lesson_id' => $this->faker->randomElement($lessonIds),
        ]);
    }
}
