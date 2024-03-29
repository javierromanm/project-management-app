<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

use App\Models\User;
use Facades\Tests\Setup\ProjectFactory; 

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_has_projects()
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(Collection::class, $user->projects);
    }

    public function test_a_user_has_accessible_projects()
    {
        $john = $this->signIn();
        ProjectFactory::ownedBy($john)->create();
        $this->assertCount(1, $john->accessibleProjects());
        $sally = User::factory()->create();
        $nick = User::factory()->create();
        $project = tap(ProjectFactory::ownedBy($sally)->create())->invite($nick);
        $this->assertCount(1, $john->accessibleProjects());   
        $project->invite($john);
        $this->assertCount(2, $john->accessibleProjects());
    }
}
