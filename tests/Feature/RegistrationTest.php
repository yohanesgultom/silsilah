<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_view_the_registration_form()
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('name="nickname"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    /** @test */
    public function guest_can_register_and_is_logged_in()
    {
        $response = $this->post(route('register'), [
            'nickname'              => 'Nick',
            'name'                  => 'Full Name',
            'gender_id'             => 1,
            'email'                 => 'newuser@example.com',
            'password'              => 'Secr3t45',
            'password_confirmation' => 'Secr3t45',
        ]);

        $response->assertRedirect('/home');

        $this->assertDatabaseHas('users', [
            'email'  => 'newuser@example.com',
            'name'   => 'Full Name',
            'gender_id' => 1,
        ]);

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertAuthenticatedAs($user);
        $this->assertEquals($user->id, $user->manager_id);
        $this->assertTrue(app('hash')->check('Secr3t45', $user->password));
    }

    /** @test */
    public function registration_requires_all_fields()
    {
        $this->post(route('register'), [])
            ->assertSessionHasErrors(['nickname', 'name', 'gender_id', 'email', 'password']);

        $this->assertGuest();
    }

    /** @test */
    public function registration_rejects_after_one_user_created()
    {
        $existing = User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('register'), [
            'nickname'              => 'Nick',
            'name'                  => 'Full Name',
            'gender_id'             => 2,
            'email'                 => 'taken@example.com',
            'password'              => 'Secr3t45',
            'password_confirmation' => 'Secr3t45',
        ])->assertForbidden();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['nickname' => 'Nick']);
    }

    /** @test */
    public function registration_requires_a_confirmed_password()
    {
        $this->post(route('register'), [
            'nickname'              => 'Nick',
            'name'                  => 'Full Name',
            'gender_id'             => 1,
            'email'                 => 'newuser@example.com',
            'password'              => 'Secr3t45',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }
}
