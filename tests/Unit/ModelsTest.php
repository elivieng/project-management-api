<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    /** Description: Verifies a project belongs to a user relationship.*/
    /** @test */
    public function check_that_a_project_belongs_to_a_user_relationship()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $project->user);
        $this->assertEquals($user->id, $project->user->id);
    }

    /** Description:  A user can have multiple projects.*/
    /** @test */
    public function check_that_a_user_can_have_multiple_projects_relationship()
    {
        $user = User::factory()->create();
        Project::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->projects);
    }

    /**Description: A project can contain multiple tasks. */
    /** @test */
    public function check_that_a_project_can_have_multiple_tasks_relationship()
    {
        $project = Project::factory()->create();
        Task::factory()->count(5)->create(['project_id' => $project->id]);

        $this->assertCount(5, $project->tasks);
    }

    /** Description: Each task belongs to a project. */
    /** @test */
    public function check_that_a_task_belongs_to_a_project_relationship()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $task->project);
        $this->assertEquals($project->id, $task->project->id);
    }

    /**Description: Each task belongs to a user. */
    /** @test */
    public function check_that_a_task_belongs_to_a_user_relationship()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    /** Description: A user can have multiple tasks.  */
    /** @test */
    public function check_that_a_user_can_have_multiple_tasks_relationship()
    {
        $user = User::factory()->create();
        Task::factory()->count(4)->create(['user_id' => $user->id]);

        $this->assertCount(4, $user->tasks);
    }

    /**Description: Verifies that projects can be soft-deleted.*/
    /** @test */
    public function check_that_a_project_can_be_soft_deleted()
    {
        $project = Project::factory()->create();
        $project->delete();

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    /**Description: Verifies that users can be soft-deleted.*/
    /** @test */
    public function check_that_a_user_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**Description: If we try to create a task with an invalid status, 
     * Laravel throws an error.*/
    /** @test */
    public function check_that_creating_a_task_with_invalid_status_fails()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Task::factory()->create(['status' => 'invalid_status']);
    }
}
