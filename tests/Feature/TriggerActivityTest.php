<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use Facades\Tests\Setup\ProjectFactory; 

class TriggerActivityTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_creating_a_project()
    {
        $project = Project::factory()->create();
        $this->assertCount(1, $project->activity);
        $this->assertEquals('created', $project->activity[0]->description);
    }

    public function test_updating_a_project()
    {
        $project = Project::factory()->create();
        $project->update(['description' => 'Changed']);
        $this->assertCount(2, $project->activity);
        $this->assertEquals('updated', $project->activity->last()->description);
    }

    public function test_creating_a_task()
    {
        $project = Project::factory()->create();
        $project->addTask('text task');
        $this->assertCount(2, $project->activity);
        $this->assertEquals('created_task', $project->activity->last()->description);
    }

    public function test_completing_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();
        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'changed',
            'completed' => true
        ]);
        $this->assertCount(3, $project->activity);
        $this->assertEquals('completed_task', $project->activity->last()->description);
    }

    public function test_incompleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();
        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'changed',
            'completed' => true
        ]);
        $this->assertCount(3, $project->activity);
        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'changed',
            'completed' => false
        ]);
        $project->refresh();
        $this->assertCount(4, $project->activity);
        $this->assertEquals('incompleted_task', $project->activity->last()->description);
    }

    public function test_deleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();
        $project->tasks[0]->delete();
        $this->assertCount(3, $project->activity);
        $this->assertEquals('deleted_task', $project->activity->last()->description);
    }
}
