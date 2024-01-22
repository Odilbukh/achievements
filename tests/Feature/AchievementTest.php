<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_get_user_achievements_data()
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaing_to_unlock_next_badge'
        ]);
    }

    public function test_unlock_first_comment_written_achievement()
    {
        Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        Artisan::call('db:seed', ['--class' => 'BadgeSeeder']);
        $user = User::factory()->create();

        $responseComment = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/comments/create', [
            'user_id' => $user->id,
            'text' => 'My comment',
        ]);

        $this->assertEquals('Comment created successfully', $responseComment['message']);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'body' => 'My comment',
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaing_to_unlock_next_badge'
        ]);

        $responseData = $response->json();
        $this->assertArrayHasKey('unlocked_achievements', $responseData);
        $this->assertEquals(['firstCommentWritten'], $responseData['unlocked_achievements']);
    }

    public function test_unlock_first_lesson_watched_achievement()
    {
        Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        Artisan::call('db:seed', ['--class' => 'BadgeSeeder']);
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
        ]);

        $this->assertEquals('The lesson watched successfully', $response['message']);

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'watched' => true,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaing_to_unlock_next_badge'
        ]);

        $responseData = $response->json();
        $this->assertArrayHasKey('unlocked_achievements', $responseData);
        $this->assertEquals(['firstLessonWatched'], $responseData['unlocked_achievements']);
    }

    public function test_record_in_achieviment_user_table()
    {
        Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        Artisan::call('db:seed', ['--class' => 'BadgeSeeder']);
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $response->assertStatus(201);

        $achievement = Achievement::where('name', 'firstLessonWatched')->first();

        $this->assertDatabaseHas('achievement_user', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
        ]);
    }

    public function test_more_user_achievements_data()
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

        $responseLesson->assertStatus(201);

        $responseComment = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/comments/create', [
            'user_id' => $user->id,
            'text' => 'My comment',
        ]);

        $responseComment->assertStatus(201);


        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaing_to_unlock_next_badge'
        ]);

        $response->assertJsonFragment([
            "unlocked_achievements" => [
                "firstLessonWatched",
                "firstCommentWritten",
            ],
            "next_available_achievements" => [
                "fiveLessonsWatched",
                "tenLessonsWatched",
                "twentyFiveLessonsWatched",
                "fiftyLessonsWatched",
                "threeCommentsWritten",
                "fiveCommentsWritten",
                "tenCommentsWritten",
                "twentyCommentsWritten",
            ],
            "current_badge" => "Beginner",
            "next_badge" => "Intermediate",
            "remaing_to_unlock_next_badge" => 2
        ]);
    }
}
