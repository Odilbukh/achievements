<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BadgeTest extends TestCase
{
    public function test_user_first_badge_data()
    {
        Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        Artisan::call('db:seed', ['--class' => 'BadgeSeeder']);
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $responseLesson = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $responseLesson->assertSuccessful();

        $responseComment = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/comments/create', [
            'user_id' => $user->id,
            'text' => 'My comment',
        ]);

        $responseComment->assertSuccessful();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertSuccessful();

        $response->assertJsonFragment([
            "current_badge" => "Beginner",
            "next_badge" => "Intermediate",
            "remaing_to_unlock_next_badge" => 2
        ]);
    }

    public function test_user_second_badge_data()
    {
        Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        Artisan::call('db:seed', ['--class' => 'BadgeSeeder']);
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $responseLesson = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $responseLesson->assertSuccessful();

        for ($i = 0; $i < 5; $i++) {
            $responseComment = $this->withHeaders([
                'Accept' => 'application/json'
            ])->post('/comments/create', [
                'user_id' => $user->id,
                'text' => 'My comment',
            ]);

            $responseComment->assertSuccessful();
        }


        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertSuccessful();

        $response->assertJsonFragment([
            "current_badge" => "Intermediate",
            "next_badge" => "Advanced",
            "remaing_to_unlock_next_badge" => 4
        ]);
    }

    public function test_user_third_badge_data()
    {
        Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        Artisan::call('db:seed', ['--class' => 'BadgeSeeder']);
        $user = User::factory()->create();
        $lesson = Lesson::factory()->count(10)->create();

        for ($i = 0; $i < 10; $i++) {
            $responseLesson = $this->withHeaders([
                'Accept' => 'application/json'
            ])->post('/lessons/watched', [
                'user_id' => $user->id,
                'lesson_id' => $i + 1,
            ]);

            $responseLesson->assertSuccessful();
        }

        for ($i = 0; $i < 20; $i++) {
            $responseComment = $this->withHeaders([
                'Accept' => 'application/json'
            ])->post('/comments/create', [
                'user_id' => $user->id,
                'text' => 'My comment',
            ]);

            $responseComment->assertSuccessful();
        }


        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertSuccessful();

        $response->assertJsonFragment([
            "current_badge" => "Advanced",
            "next_badge" => "Master",
            "remaing_to_unlock_next_badge" => 2
        ]);
    }

    public function test_user_fourth_badge_data()
    {
        Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        Artisan::call('db:seed', ['--class' => 'BadgeSeeder']);
        $user = User::factory()->create();
        $lesson = Lesson::factory()->count(50)->create();

        for ($i = 0; $i < 50; $i++) {
            $responseLesson = $this->withHeaders([
                'Accept' => 'application/json'
            ])->post('/lessons/watched', [
                'user_id' => $user->id,
                'lesson_id' => $i + 1,
            ]);

            $responseLesson->assertSuccessful();
        }

        for ($i = 0; $i < 20; $i++) {
            $responseComment = $this->withHeaders([
                'Accept' => 'application/json'
            ])->post('/comments/create', [
                'user_id' => $user->id,
                'text' => 'My comment',
            ]);

            $responseComment->assertSuccessful();
        }


        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertSuccessful();
dd($user);
        $response->assertJsonFragment([
            "current_badge" => "Master",
            "next_badge" => null,
            "remaing_to_unlock_next_badge" => 0
        ]);
    }
}
