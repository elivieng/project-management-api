<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class ProjectTest extends TestCase
{
    use RefreshDatabase; 

    private function login()
    {
        $user = User::factory()->create(); 
        $this->actingAs($user); 
        return $user;
    }

    /** Description: Check create a project endpoint with tasks.  */
    /** @test */
    public function check_create_a_project_endpoint()
    {
        $user = User::factory()->create();

        $projectData = [
            'name' => 'Test Project',
            'description' => 'Project description',
            'tasks' => [
                [
                    'title' => 'Task 1',
                    'description' => 'Description 1',
                    'due_date'=> '2025-03-29T23:09:32',
                    'status' => 'pending',
                    'user_id' => $user->id,
                ],
                [
                    'title' => 'Task 2',
                    'description' => 'Description 2',
                    'due_date'=> '2025-03-29T23:09:32',
                    'status' => 'in progress',
                    'user_id' => $user->id,
                ]
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/projects', $projectData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test Project']);

        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
        $this->assertDatabaseHas('tasks', ['title' => 'Task 1']);
        $this->assertDatabaseHas('tasks', ['title' => 'Task 2']);
    }

    /** Description: Validates that the name is required and the description 
     * is a string when creating a project. */
    /** @test */
    public function check_name_is_required_and_description_should_be_string()
    {
        $user = $this->login();

        $responseWithoutName = $this->postJson('/api/projects', [
            'description' => 'Valid description'
        ]);
        
        $responseWithoutName->assertStatus(422); 
        $responseWithoutName->assertJsonValidationErrors('name'); 

        $responseWithInvalidDescription = $this->postJson('/api/projects', [
            'name' => 'project',
            'description' => 12345
        ]);

        $responseWithInvalidDescription->assertStatus(422); 
        
        $responseWithInvalidDescription->assertJsonValidationErrors('description'); 
    }

    /** Description: Verify that a project with tasks in the completed state cannot be deleted. */
    /** @test */
    public function check_project_with_completed_task_can_not_be_deleted()
    {
        $user = $this->login();

        $project = Project::create([
            'name' => 'Project with completed task',
            'description' => 'A project with a task that is completed',
            'user_id' => $user->id, 
        ]);

        $task = Task::create([
            'title' => 'Completed Task',
            'description' => 'This task is completed',
            'due_date'=> '2025-03-29T23:09:32',
            'status' => 'completed',
            'project_id' => $project->id,
            'user_id' => $user->id, 
        ]);

        // Tries delete the project.
        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(400); 
        $response->assertJson([
            'error' => [
                'message' => 'Only projects without tasks or with pending tasks can be deleted.'
            ]
        ]);
        
    }

    /* 
    * Description: Verify that a project with pending tasks can be deleted
    */
    /** @test */
    public function check_a_project_with_pending_tasks_can_be_deleted()
    {
        $user = $this->login();

        $project = Project::create([
            'name' => 'Project with pending task',
            'description' => 'A project with a pending task',
            'user_id' => $user->id,
        ]);

        $task = Task::create([
            'title' => 'Pending Task',
            'description' => 'This task is pending',
            'status' => 'pending',
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
        ]);

        $this->assertNotNull($project->fresh()->deleted_at);
    }

    /** Description: Verify that a project without tasks can also be deleted. */
    /** @test */
    public function check_a_project_without_tasks_can_be_deleted()
    {
        $user = $this->login();

        $project = Project::create([
            'name' => 'Project without tasks',
            'description' => 'A project without tasks',
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);

        $project->refresh();

        $this->assertNotNull($project->deleted_at);
    }

    /** Description: Verify that the update project endpoint updates it correctly 
     * when it receives only the name, without modifying the description.   */
    /** @test */
    public function check_updates_project_without_description()
    {
        $user = $this->login();

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'description' => 'Old Description',
        ]);

        $updateData = [
            'name' => 'Updated Name'
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $project->id,
                    'name' => 'Updated Name',
                ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Name',
        ]);

        // Verify that the description of the project was not set as null
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'description' => 'Old Description',
        ]);
    }
}
