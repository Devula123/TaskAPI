<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_tasks(): void
    {
        Task::factory()->create(['title' => 'First task']);
        Task::factory()->create(['title' => 'Second task']);

        $response = $this->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'title', 'status', 'created_at', 'updated_at']]]);
    }

    public function test_it_creates_task_with_default_status_todo(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'New task',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'New task')
            ->assertJsonPath('data.status', TaskStatus::Todo->value);
    }

    public function test_it_validates_create_request(): void
    {
        $response = $this->postJson('/api/tasks', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_it_shows_single_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $task->id);
    }

    public function test_it_returns_404_for_missing_task(): void
    {
        $response = $this->getJson('/api/tasks/999');

        $response
            ->assertNotFound()
            ->assertJsonPath('message', 'Task not found.');
    }

    public function test_it_updates_title(): void
    {
        $task = Task::factory()->create(['title' => 'Old']);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated');
    }

    public function test_it_allows_valid_status_transition(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::Todo,
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => TaskStatus::InProgress->value,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', TaskStatus::InProgress->value);
    }

    public function test_it_rejects_invalid_status_transition(): void
    {
        $task = Task::factory()->create([
            'status' => TaskStatus::Todo,
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => TaskStatus::Done->value,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_it_deletes_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}

