<?php

namespace Tests\Unit\Helpers;

use App\User;
use Tests\TestCase;

class IsSystemAdminHelperTest extends TestCase
{
    /** @test */
    public function user_is_an_admin()
    {
        $admin1 = User::factory()->make(['email' => $adminEmail1 = 'admin1@example.net']);
        $admin2 = User::factory()->make(['email' => $adminEmail2 = 'admin2@example.net']);
        $userWithEmail = User::factory()->make(['email' => 'user@example.net']);
        $userWithNoEmail = User::factory()->make(['email' => null]);

        $this->assertTrue($admin1->is_system_admin());
        $this->assertTrue($admin2->is_system_admin());
        $this->assertFalse($userWithEmail->is_system_admin());
        $this->assertFalse($userWithNoEmail->is_system_admin());
    }

    /** @test */
    public function if_config_is_null()
    {
        $admin1 = User::factory()->make(['email' => $adminEmail1 = 'admin1@example.net']);
        $admin2 = User::factory()->make(['email' => $adminEmail2 = 'admin2@example.net']);
        $userWithEmail = User::factory()->make(['email' => 'user@example.net']);
        $userWithNoEmail = User::factory()->make(['email' => null]);

        config(['app.system_admin_emails' => '']);
        $this->assertFalse($admin1->is_system_admin());
        $this->assertFalse($admin2->is_system_admin());
        $this->assertFalse($userWithEmail->is_system_admin());
        $this->assertFalse($userWithNoEmail->is_system_admin());
    }
}