<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**Description: Verify create task endpoint. */
    /** @test */
    public function check_create_a_task_endpoint()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                 ->assertJson([
                     'title' => 'New Task',
                     'status' => 'pending',
                 ]);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    /**Description: Verify the list all tasks endpoint */
    /** @test */
    public function check_list_all_tasks_endpoint()
    {
        Task::factory()->count(3)->create(['project_id' => $this->project->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**Description: Verify the show task endpoint.*/
    /** @test */
    public function check_show_a_task_endpoint()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $task->id,
                     'title' => $task->title,
                 ]);
    }

    /**Description: Verify the update task endpoint. */
    /** @test */
    public function check_update_a_task_endpoint()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $updateData = [
            'title' => 'Updated Task',
            'status' => 'completed',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $task->id,
                     'title' => 'Updated Task',
                     'status' => 'completed',
                 ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Updated Task']);
    }

    /**Description: Verify the delete task endpoint. */
    /** @test */
    public function check_delete_a_task_endpoint()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
