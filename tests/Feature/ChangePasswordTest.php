<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_change_password()
    {
        $user = $this->loginAsUser();

        // Test changing password - route is POST not PATCH
        $response = $this->post(route('password_update'), [
            'old_password' => 'secret',
            'new_password' => 'newpassword',
            'new_password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('profile'));
    }

    /** @test */
    public function user_cannot_change_password_if_old_password_wrong()
    {
        $user = $this->loginAsUser();

        $response = $this->post(route('password_update'), [
            'old_password' => 'wrongpassword',
            'new_password' => 'newpassword',
            'new_password_confirmation' => 'newpassword',
        ]);

        $response->assertSessionHasErrors('old_password');
        $response->assertStatus(302);
    }
}