<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('auth_token')->plainTextToken;
    }

    public function test_user_can_create_task()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/tasks', [
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'due_date' => now()->addDays(1)->format('Y-m-d')
                ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['task']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_view_their_tasks()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id, 'title' => 'Test Task', 'description' => 'Test Description', 'due_date' => now()]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_view_other_users_tasks()
    {
        $otherUser = User::factory()->create();
        Task::factory()->create(['user_id' => $otherUser->id, 'title' => 'Test Task', 'description' => 'Test Description', 'due_date' => now()]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_user_can_update_their_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Test Task', 'description' => 'Test Description', 'due_date' => now()]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->putJson("/api/tasks/{$task->id}", [
                    'title' => 'Updated Title',
                ]);

        $response->assertStatus(200)
            ->assertJson(['title' => 'Updated Title']);
    }

    public function test_user_cannot_update_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id, 'title' => 'Test Task', 'description' => 'Test Description', 'due_date' => now()]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->putJson("/api/tasks/{$task->id}", [
                    'title' => 'Updated Title',
                ]);

        $response->assertStatus(404);
    }

    public function test_user_can_delete_their_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, 'title' => 'Test Task', 'description' => 'Test Description', 'due_date' => now()]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
    }

    public function test_user_cannot_delete_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id, 'title' => 'Test Task', 'description' => 'Test Description', 'due_date' => now()]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(404);
    }
}