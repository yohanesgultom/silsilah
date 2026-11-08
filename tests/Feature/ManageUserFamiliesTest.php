<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageUserFamiliesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_update_their_father()
    {
        $user = $this->loginAsUser();

        // Visit the profile page with set_father action
        $this->get(route('users.show', [$user->id, 'action' => 'set_father']))
            ->assertSee('set_father')
            ->assertSee(__('app.enter_new_name'));

        // Submit the form to set father
        $this->post(route('family-actions.set-father', $user->id), [
            'set_father' => 'Nama Ayah',
        ]);

        $this->assertDatabaseHas('users', [
            'nickname' => 'Nama Ayah',
        ]);

        $this->assertEquals('Nama Ayah', $user->fresh()->father->nickname);
    }

    /** @test */
    public function user_can_update_their_mother()
    {
        $user = $this->loginAsUser();

        $this->get(route('users.show', [$user->id, 'action' => 'set_mother']))
            ->assertSee('set_mother')
            ->assertSee(__('app.enter_new_name'));

        $this->post(route('family-actions.set-mother', $user->id), [
            'set_mother' => 'Nama Ibu',
        ]);

        $this->assertDatabaseHas('users', [
            'nickname'   => 'Nama Ibu',
            'manager_id' => $user->id,
        ]);

        $this->assertEquals('Nama Ibu', $user->fresh()->mother->nickname);
    }

    /** @test */
    public function user_can_add_childrens()
    {
        $user = $this->loginAsUser(['gender_id' => 1]);

        $this->post(route('family-actions.add-child', $user->id), [
            'add_child_name'      => 'Nama Anak 1',
            'add_child_gender_id' => 1,
            'add_child_parent_id' => '',
        ]);

        $this->assertDatabaseHas('users', [
            'nickname'   => 'Nama Anak 1',
            'gender_id'  => 1,
            'father_id'  => $user->id,
            'mother_id'  => null,
            'parent_id'  => null,
            'manager_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_add_childrens_with_parent_id_if_exist()
    {
        $husband = $this->loginAsUser(['gender_id' => 1]);
        $wife = User::factory()->female()->create(['manager_id' => $husband->id]);
        $husband->addWife($wife);

        $marriageId = $husband->fresh()->wifes->first()->pivot->id;

        $this->post(route('family-actions.add-child', $husband->id), [
            'add_child_name'      => 'Nama Anak 1',
            'add_child_gender_id' => 1,
            'add_child_parent_id' => $marriageId,
        ]);

        $this->assertDatabaseHas('users', [
            'nickname'   => 'Nama Anak 1',
            'gender_id'  => 1,
            'father_id'  => $husband->id,
            'mother_id'  => $wife->id,
            'manager_id' => $husband->id,
        ]);
    }

    /** @test */
    public function user_can_add_children_with_birth_order()
    {
        $user = $this->loginAsUser(['gender_id' => 1]);

        $this->post(route('family-actions.add-child', $user->id), [
            'add_child_name'        => 'Nama Anak 1',
            'add_child_gender_id'   => 1,
            'add_child_birth_order' => 2,
            'add_child_parent_id'   => '',
        ]);

        $this->assertDatabaseHas('users', [
            'nickname'    => 'Nama Anak 1',
            'gender_id'   => 1,
            'father_id'   => $user->id,
            'mother_id'   => null,
            'parent_id'   => null,
            'manager_id'  => $user->id,
            'birth_order' => 2,
        ]);
    }

    /** @test */
    public function user_can_set_wife()
    {
        $user = $this->loginAsUser(['gender_id' => 1]);

        $this->post(route('family-actions.add-wife', $user->id), [
            'set_wife'      => 'Nama Istri',
            'marriage_date' => '2010-01-01',
        ]);

        $this->assertDatabaseHas('users', [
            'nickname'  => 'Nama Istri',
            'gender_id' => 2,
        ]);

        $wife = User::where([
            'nickname'  => 'Nama Istri',
            'gender_id' => 2,
        ])->first();

        $this->assertDatabaseHas('couples', [
            'husband_id'    => $user->id,
            'wife_id'       => $wife->id,
            'marriage_date' => '2010-01-01',
            'manager_id'    => $user->id,
        ]);
    }

    /** @test */
    public function user_can_set_husband()
    {
        $user = $this->loginAsUser(['gender_id' => 2]);

        $this->post(route('family-actions.add-husband', $user->id), [
            'set_husband'   => 'Nama Suami',
            'marriage_date' => '2010-03-03',
        ]);

        $this->assertDatabaseHas('users', [
            'nickname'   => 'Nama Suami',
            'gender_id'  => 1,
            'manager_id' => $user->id,
        ]);

        $husband = User::where([
            'nickname'  => 'Nama Suami',
            'gender_id' => 1,
        ])->first();

        $this->assertDatabaseHas('couples', [
            'husband_id'    => $husband->id,
            'wife_id'       => $user->id,
            'marriage_date' => '2010-03-03',
            'manager_id'    => $user->id,
        ]);
    }

    /** @test */
    public function user_can_pick_father_from_existing_user()
    {
        $user = $this->loginAsUser();
        $father = User::factory()->male()->create();

        $this->post(route('family-actions.set-father', $user->id), [
            'set_father'    => '',
            'set_father_id' => $father->id,
        ]);

        $this->assertEquals($father->nickname, $user->fresh()->father->nickname);
    }

    /** @test */
    public function user_can_pick_mother_from_existing_user()
    {
        $user = $this->loginAsUser();
        $mother = User::factory()->female()->create();

        $this->post(route('family-actions.set-mother', $user->id), [
            'set_mother'    => '',
            'set_mother_id' => $mother->id,
        ]);

        $this->assertEquals($mother->nickname, $user->fresh()->mother->nickname);
    }

    /** @test */
    public function user_can_pick_wife_from_existing_user()
    {
        $user = $this->loginAsUser(['gender_id' => 1]);
        $wife = User::factory()->female()->create();

        $this->post(route('family-actions.add-wife', $user->id), [
            'set_wife'      => '',
            'set_wife_id'   => $wife->id,
            'marriage_date' => '2010-01-01',
        ]);

        $this->assertDatabaseHas('couples', [
            'husband_id'    => $user->id,
            'wife_id'       => $wife->id,
            'marriage_date' => '2010-01-01',
            'manager_id'    => $user->id,
        ]);
    }

    /** @test */
    public function user_can_pick_husband_from_existing_user()
    {
        $user = $this->loginAsUser(['gender_id' => 2]);
        $husband = User::factory()->male()->create();

        $this->post(route('family-actions.add-husband', $user->id), [
            'set_husband'    => '',
            'set_husband_id' => $husband->id,
            'marriage_date'  => '2010-03-03',
        ]);

        $this->assertDatabaseHas('couples', [
            'husband_id'    => $husband->id,
            'wife_id'       => $user->id,
            'marriage_date' => '2010-03-03',
            'manager_id'    => $user->id,
        ]);
    }

    /** @test */
    public function user_can_set_parent_from_existing_couple_id()
    {
        $user = $this->loginAsUser();
        $husband = User::factory()->male()->create();
        $wife = User::factory()->female()->create();
        $husband->addWife($wife);

        $marriageId = $husband->fresh()->wifes->first()->pivot->id;

        $this->post(route('family-actions.set-parent', $user->id), [
            'set_parent_id' => $marriageId,
        ]);

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'parent_id'  => $marriageId,
            'manager_id' => $user->id,
        ]);
    }
}