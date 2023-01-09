<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use App\Models\Task;
use Facades\Tests\Setup\ProjectFactory; 

class TriggerActivityTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_creating_a_project()
    {
        $project = Project::factory()->create();
        $this->assertCount(1, $project->activity);

        tap($project->activity[0], function($activity) {  
            $this->assertEquals('created', $activity->description);            
            $this->assertNull($activity->changes);

        });

        
    }

    public function test_updating_a_project()
    {
        $project = Project::factory()->create();
        $originalTitle = $project->title;
        $project->update(['title' => 'Changed']);
        $this->assertCount(2, $project->activity);
        
        tap($project->activity->last(), function($activity) use ($originalTitle) {               
            $this->assertEquals('updated', $activity->description);
            $expected = [
                'before' => ['title' => $originalTitle],
                'after' => ['title' => 'Changed']
            ];
            $this->assertEquals($expected, $activity->changes);

        });
        
    }

    public function test_creating_a_task()
    {
        $project = Project::factory()->create();
        $project->addTask('text task');
        $this->assertCount(2, $project->activity);
        tap($project->activity->last(), function($activity) {
            $this->assertEquals('created_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals('text task', $activity->subject->body);
        });
        
    }

    public function test_completing_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();
        $this->actingAs($project->owner)->patch($project->tasks[0]->path(), [
            'body' => 'changed',
            'completed' => true
        ]);
        $this->assertCount(3, $project->activity);
        tap($project->activity->last(), function($activity) {
            $this->assertEquals('completed_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals('changed', $activity->subject->body);
        });
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
        tap($project->activity->last(), function($activity) {
            $this->assertEquals('incompleted_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals('changed', $activity->subject->body);
        });
    }

    public function test_deleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();
        $project->tasks[0]->delete();
        $this->assertCount(3, $project->activity);
        $this->assertEquals('deleted_task', $project->activity->last()->description);
    }
}
