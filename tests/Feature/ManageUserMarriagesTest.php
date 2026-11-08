<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageUserMarriagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_visit_other_user_marriages_page()
    {
        $user = $this->loginAsUser();
        $otherUser = User::factory()->create();

        $this->get(route('users.marriages', $otherUser))->assertSee($otherUser->name);
    }
}