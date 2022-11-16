<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use App\Models\User;

class ProjectsTest extends TestCase
{    
    use WithFaker, RefreshDatabase;

    public function test_gests_cannot_create_projects()
    {
        $attributes = Project::factory()->raw();
        $this->post('/projects', $attributes)->assertRedirect('login');        
    }

    public function test_guests_cannot_view_projects()
    {
        $this->get('/projects')->assertRedirect('login');        
    }

    public function test_guests_cannot_view_a_single_project()
    {
        $project = Project::factory()->create();
        $this->get($project->path())->assertRedirect('login');  
    }

    public function test_a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph

        ];

        $this->post('/projects', $attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }

    public function test_a_user_can_view_a_project()
    {
        $this->actingAs(User::factory()->create());
        $this->withoutExceptionHandling();
        $project = Project::factory()->create(['owner_id' => auth()->user()->id]);
        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    public function test_an_authenticated_user_cannot_view_the_projects_of_others()
    {
        $this->actingAs(User::factory()->create());
        // $this->withoutExceptionHandling();
        $project = Project::factory()->create();
        $this->get($project->path())->assertStatus(403);
    }

    public function test_a_project_requires_a_title()
    {
        $this->actingAs(User::factory()->create());
        $attributes = Project::factory()->raw(['title' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
        
    }

    public function test_a_project_requires_a_description()
    {
        $this->actingAs(User::factory()->create());
        $attributes = Project::factory()->raw(['description' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
        
    }
}
