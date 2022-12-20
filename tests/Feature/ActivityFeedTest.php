<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use Facades\Tests\Setup\ProjectFactory; 

class ActivityFeedTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_creating_a_project_generates_activity()
    {
        $project = Project::factory()->create();
        $this->assertCount(1, $project->activity);
        $this->assertEquals('created', $project->activity[0]->description);
    }

    public function test_updating_a_project_generates_activity()
    {
        $project = Project::factory()->create();
        $project->update(['description' => 'Changed']);
        $this->assertCount(2, $project->activity);
        $this->assertEquals('updated', $project->activity->last()->description);
    }
}
