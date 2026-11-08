<?php

namespace Tests\Unit\Policies;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function manager_can_edit_users_profile()
    {
        $manager = User::factory()->create();
        $user = User::factory()->create(['manager_id' => $manager->id]);

        $this->assertTrue($manager->can('edit', $user));
    }

    /** @test */
    public function admins_can_edit_any_user_profile()
    {
        $adminEmail = 'admin@example.net';
        config(['app.system_admin_emails' => $adminEmail]);

        $manager = User::factory()->create();
        $admin = User::factory()->create(['email' => $adminEmail]);
        $user = User::factory()->create(['manager_id' => $manager->id]);
        $otherUser = User::factory()->create(['manager_id' => $manager->id]);

        $this->assertTrue($admin->can('edit', $manager));
        $this->assertTrue($admin->can('edit', $user));
        $this->assertTrue($admin->can('edit', $otherUser));
        $this->assertTrue($manager->can('edit', $user));
    }

    /** @test */
    public function user_can_edit_their_own_profile()
    {
        $user = User::factory()->create();

        $this->assertTrue($user->can('edit', $user));
    }

    /** @test */
    public function manager_can_delete_a_user()
    {
        $manager = User::factory()->create();
        $user = User::factory()->create(['manager_id' => $manager->id]);

        $this->assertTrue($manager->can('delete', $user));
    }

    /** @test */
    public function admins_can_delete_any_user()
    {
        $adminEmail = 'admin@example.net';
        config(['app.system_admin_emails' => $adminEmail]);

        $manager = User::factory()->create();
        $admin = User::factory()->create(['email' => $adminEmail]);
        $user = User::factory()->create(['manager_id' => $manager->id]);

        $this->assertTrue($admin->can('delete', $manager));
        $this->assertTrue($admin->can('delete', $user));
        $this->assertFalse($user->can('delete', $user));
    }

    /** @test */
    public function user_cannot_delete_their_own_data()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('delete', $user));
    }
}