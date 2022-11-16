<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Project;
use App\Models\User;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_has_a_path()
    {       
        $project = Project::factory()->create();
        $this->assertEquals($project->path(), $project->path());
    }

    public function test_it_belongs_to_an_owner()
    {       
        $project = Project::factory()->create();
        $this->assertInstanceOf(User::class, $project->owner);
    }
}
