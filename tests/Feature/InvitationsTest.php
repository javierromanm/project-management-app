<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use App\Models\User;
use Facades\Tests\Setup\ProjectFactory; 

class InvitationsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_a_project_can_invite_users()
    {
        $project = ProjectFactory::create();  
        $project->invite($newUser = User::factory()->create());
        $this->signIn($newUser);
        $this->post("/projects/{$project->id}/tasks", $task = ['body' => 'Foo task']);
        $this->assertDatabaseHas('tasks', $task);
    }
}
