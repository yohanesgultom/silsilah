<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonRelationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_user_model_with_factory()
    {
        $person = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'nickname' => $person->nickname,
            'gender_id' => $person->gender_id,
        ]);
    }

    /** @test */
    public function person_can_have_a_father()
    {
        $person = User::factory()->create();
        $father = User::factory()->male()->create();
        $person->setFather($father);

        $this->assertDatabaseHas('users', [
            'id' => $person->id,
            'father_id' => $father->id,
        ]);

        $this->assertEquals($father->name, $person->father->name);
    }

    /** @test */
    public function person_can_have_a_mother()
    {
        $person = User::factory()->create();
        $mother = User::factory()->female()->create();
        $person->setMother($mother);

        $this->assertDatabaseHas('users', [
            'id' => $person->id,
            'mother_id' => $mother->id,
        ]);

        $this->assertEquals($mother->name, $person->mother->name);
    }

    /** @test */
    public function person_can_many_childs()
    {
        $mother = User::factory()->female()->create();
        $person = User::factory()->create();
        $person->setMother($mother);
        $person = User::factory()->create();
        $person->setMother($mother);

        $this->assertCount(2, $mother->childs);
    }
}