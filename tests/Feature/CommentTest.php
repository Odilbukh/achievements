<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_watch_lesson_with_correct_data(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/comments/create', [
            'user_id' => $user->id,
            'text' => 'My comment',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
        ]);

        $this->assertEquals('Comment created successfully', $response['message']);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'body' => 'My comment',
        ]);
    }

    public function test_watch_lesson_with_incorrect_data(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/comments/create', [
            'text' => 123,
            'user_id' => 'User name'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);

        $responseData = $response->json();

        $this->assertArrayHasKey('user_id', $responseData['errors']);
        $this->assertEquals(['The selected user id is invalid.'], $responseData['errors']['user_id']);
        $this->assertArrayHasKey('text', $responseData['errors']);
        $this->assertEquals(['The text field must be a string.'], $responseData['errors']['text']);
    }

    public function test_watch_lesson_without_text(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/comments/create', [
            'user_id' => $user->id
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);

        $responseData = $response->json();
        $this->assertArrayHasKey('text', $responseData['errors']);
        $this->assertEquals(['The text field is required.'], $responseData['errors']['text']);
    }

    public function test_watch_lesson_without_user_id(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/comments/create', [
            'text' => 'Some text'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);

        $responseData = $response->json();
        $this->assertArrayHasKey('user_id', $responseData['errors']);
        $this->assertEquals(['The user id field is required.'], $responseData['errors']['user_id']);
    }
}
