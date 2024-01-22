<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_watch_lesson_with_correct_data(): void
    {
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
    }

    public function test_watch_lesson_with_incorrect_data(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'lesson_id' => 'Lesson name',
            'user_id' => 'User name'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);

        $responseData = $response->json();
        $this->assertArrayHasKey('lesson_id', $responseData['errors']);
        $this->assertEquals(['The selected lesson id is invalid.'], $responseData['errors']['lesson_id']);
        $this->assertArrayHasKey('user_id', $responseData['errors']);
        $this->assertEquals(['The selected user id is invalid.'], $responseData['errors']['user_id']);
    }

    public function test_watch_lesson_without_lesson_id(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'user_id' => $user->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);

        $responseData = $response->json();
        $this->assertArrayHasKey('lesson_id', $responseData['errors']);
        $this->assertEquals(['The lesson id field is required.'], $responseData['errors']['lesson_id']);
    }

    public function test_watch_lesson_without_user_id(): void
    {
        $lesson = Lesson::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'lesson_id' => $lesson->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);

        $responseData = $response->json();
        $this->assertArrayHasKey('user_id', $responseData['errors']);
        $this->assertEquals(['The user id field is required.'], $responseData['errors']['user_id']);
    }

    public function test_already_watched_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
        ]);

        $this->assertEquals('The lesson watched successfully', $response['message']);

        $this->assertDatabaseHas('lesson_user', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $responseSecond = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/lessons/watched', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'watched' => true,
        ]);

        $this->assertEquals('The lesson was watched already', $responseSecond['message']);
    }
}
