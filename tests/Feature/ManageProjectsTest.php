<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use App\Models\User;
use Facades\Tests\Setup\ProjectFactory; 

class ManageProjectsTest extends TestCase
{    
    use WithFaker, RefreshDatabase;

    public function test_gests_cannot_manage_projects()
    {
        $project = Project::factory()->create();    
        $this->get('/projects')->assertRedirect('login');  
        $this->get('/projects/create')->assertRedirect('login');  
        $this->get($project->path() . '/edit')->assertRedirect('login');  
        $this->get($project->path())->assertRedirect('login');     
        $this->post('/projects', $project->toArray())->assertRedirect('login');        
    }   

    public function test_a_user_can_create_a_project()
    {        
        $user = $this->signIn();

        $this->get('/projects/create')->assertStatus(200);

        $attributes = Project::factory()->raw(['owner_id' => $user->id]);

        $this->followingRedirects()->post('/projects', $attributes)
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);

        $this->assertDatabaseHas('projects', $attributes);
    }

    public function test_a_user_can_delete_a_project()
    {        
        $project = ProjectFactory::create();           

        $this->actingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', $project->only('id'));
    }

    public function test_unauthorized_users_cannot_delete_projects()
    {        
        $project = ProjectFactory::create();           

        $this->delete($project->path())
            ->assertRedirect('/login');

        $user = $this->signIn();

        $this->delete($project->path())->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)->delete($project->path())->assertStatus(403);
    }

    public function test_a_user_can_view_a_project()
    {
        $project = ProjectFactory::create();  
        $this->actingAs($project->owner)
            ->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    public function test_a_user_can_see_projects_they_have_been_invited_to_on_their_dashboard()
    {
        $user = $this->signIn();
        $project = tap(ProjectFactory::create())->invite($user);
        $this->get('/projects')->assertSee($project->title);
    }

    public function test_a_user_can_update_a_project()
    {        
        $project = ProjectFactory::create();           

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = ['title' => 'Changed', 'description' => 'Changed', 'notes' => 'Changed'])
            ->assertRedirect($project->path());

        $this->get($project->path() . '/edit')->assertStatus(200);

        $this->assertDatabaseHas('projects', $attributes);

        $this->get($project->path())
            ->assertSee('Changed');
    }

    public function test_a_user_can_update_a_project_notes()
    {        
        $project = ProjectFactory::create();           

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes = ['notes' => 'Changed']);
 
        $this->assertDatabaseHas('projects', $attributes);
    }

    public function test_an_authenticated_user_cannot_view_the_projects_of_others()
    {
        $this->signIn();        
        $project = Project::factory()->create();
        $this->get($project->path())->assertStatus(403);
    }

    public function test_an_authenticated_user_cannot_update_the_projects_of_others()
    {
        $this->signIn();       
        $project = Project::factory()->create();
        $this->patch($project->path(), [])->assertStatus(403);
    }

    public function test_a_project_requires_a_title()
    {
        $this->signIn();
        $attributes = Project::factory()->raw(['title' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
        
    }

    public function test_a_project_requires_a_description()
    {
        $this->signIn();
        $attributes = Project::factory()->raw(['description' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
        
    }
}
