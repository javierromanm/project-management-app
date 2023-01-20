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

    public function test_non_owners_may_not_invite_users()
    {
        $project = ProjectFactory::create();  
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post($project->path() . '/invitations')
            ->assertStatus(403);

    }

    public function test_a_project_owner_can_invite_users()
    {
        $project = ProjectFactory::create();  
        $userToInvite = User::factory()->create();
        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => $userToInvite->email
            ])->assertRedirect($project->path());
        $this->assertTrue($project->members->contains($userToInvite));
    }

    public function test_the_email_must_be_associated_with_a_valid_account()
    {
        $project = ProjectFactory::create();          
        $this->actingAs($project->owner)
            ->post($project->path() . '/invitations', [
                'email' => 'notauser@example.com'
            ])->assertSessionHasErrors([
                'email' => 'The user you are trying to invite must have a valid account'
            ]);

    }

    public function test_an_invited_user_may_update_project_details()
    {
        $project = ProjectFactory::create();  
        $project->invite($newUser = User::factory()->create());
        $this->signIn($newUser);
        $this->post("/projects/{$project->id}/tasks", $task = ['body' => 'Foo task']);
        $this->assertDatabaseHas('tasks', $task);
    }
}
