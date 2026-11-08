<?php

namespace Tests\Unit\Policies;

use App\Couple;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CouplePolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function manager_can_edit_couples()
    {
        $otherCoupleManagerId = Str::random();
        $manager = User::factory()->create();
        $couple = Couple::factory()->create(['manager_id' => $manager->id]);
        $otherCouple = Couple::factory()->create(['manager_id' => $otherCoupleManagerId]);

        $this->assertTrue($manager->can('edit', $couple));
        $this->assertFalse($manager->can('edit', $otherCouple));
    }

    /** @test */
    public function admins_can_edit_any_couple_data()
    {
        $adminEmail = 'admin@example.net';
        $otherCoupleManagerId = Str::random();
        config(['app.system_admin_emails' => $adminEmail]);

        $manager = User::factory()->create();
        $admin = User::factory()->create(['email' => $adminEmail]);
        $couple = Couple::factory()->create(['manager_id' => $manager->id]);
        $otherCouple = Couple::factory()->create(['manager_id' => $otherCoupleManagerId]);

        $this->assertTrue($admin->can('edit', $couple));
        $this->assertTrue($admin->can('edit', $otherCouple));

        $this->assertTrue($manager->can('edit', $couple));
        $this->assertFalse($manager->can('edit', $otherCouple));
    }
}