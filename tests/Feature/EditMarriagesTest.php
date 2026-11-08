<?php

namespace Tests\Feature;

use App\Couple;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditMarriagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_visit_a_marriage_detail_page()
    {
        $user = $this->loginAsUser();
        $couple = Couple::factory()->create(['manager_id' => $user->id]);

        $this->get(route('couples.show', $couple))->assertSee($couple->husband->name);
        $this->get(route('couples.show', $couple))->assertSee($couple->wife->name);
    }

    /** @test */
    public function manager_can_edit_couple_data()
    {
        $user = $this->loginAsUser();
        $couple = Couple::factory()->create(['manager_id' => $user->id]);

        $this->patch(route('couples.update', $couple), [
            'marriage_date' => '2015-10-17',
            'divorce_date' => '2017-10-17',
        ]);

        $this->assertEquals('2015-10-17', $couple->fresh()->marriage_date->format('Y-m-d'));
        $this->assertEquals('2017-10-17', $couple->fresh()->divorce_date->format('Y-m-d'));
    }
}