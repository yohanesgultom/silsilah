<?php

namespace Tests\Feature;

use App\Couple;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function manager_can_delete_a_user()
    {
        $manager = $this->loginAsUser();
        $user = User::factory()->create(['manager_id' => $manager->id]);

        // Visit delete confirmation page (GET to edit with action=delete)
        $response = $this->get(route('users.edit', [$user, 'action' => 'delete']));
        $response->assertStatus(200);

        // Submit delete form (POST to destroy with _method=DELETE and user_id)
        $response = $this->delete(route('users.destroy', $user), [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function manager_can_delete_a_user_the_replace_childs_father_id()
    {
        $manager = $this->loginAsUser();
        $oldUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);
        $oldUserChild = User::factory()->create(['father_id' => $oldUser->id]);
        $replacementUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $oldUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'father_id' => $oldUser->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id'        => $oldUserChild->id,
            'father_id' => $replacementUser->id,
        ]);
    }

    /** @test */
    public function manager_can_delete_a_user_the_replace_childs_mother_id()
    {
        $manager = $this->loginAsUser();
        $oldUser = User::factory()->female()->create([
            'manager_id' => $manager->id,
        ]);
        $oldUserChild = User::factory()->create(['mother_id' => $oldUser->id]);
        $replacementUser = User::factory()->female()->create([
            'manager_id' => $manager->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $oldUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'mother_id' => $oldUser->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id'        => $oldUserChild->id,
            'mother_id' => $replacementUser->id,
        ]);
    }

    /** @test */
    public function manager_can_delete_a_user_the_replace_users_manager_id()
    {
        $manager = $this->loginAsUser();
        $oldUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);
        $oldUserManagedUser = User::factory()->create(['manager_id' => $oldUser->id]);
        $replacementUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $oldUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'manager_id' => $oldUser->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id'         => $oldUserManagedUser->id,
            'manager_id' => $replacementUser->id,
        ]);
    }

    /** @test */
    public function manager_can_delete_a_user_the_replace_couples_husband_id()
    {
        $manager = $this->loginAsUser();
        $oldUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);
        $oldUserCouple = Couple::factory()->create([
            'husband_id' => $oldUser->id,
        ]);
        $replacementUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $oldUser->id,
        ]);

        $this->assertDatabaseMissing('couples', [
            'husband_id' => $oldUser->id,
        ]);

        $this->assertDatabaseHas('couples', [
            'id'         => $oldUserCouple->id,
            'husband_id' => $replacementUser->id,
        ]);
    }

    /** @test */
    public function manager_can_delete_a_user_the_replace_couples_wife_id()
    {
        $manager = $this->loginAsUser();
        $oldUser = User::factory()->female()->create([
            'manager_id' => $manager->id,
        ]);
        $oldUserCouple = Couple::factory()->create([
            'wife_id' => $oldUser->id,
        ]);
        $replacementUser = User::factory()->female()->create([
            'manager_id' => $manager->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $oldUser->id,
        ]);

        $this->assertDatabaseMissing('couples', [
            'wife_id' => $oldUser->id,
        ]);

        $this->assertDatabaseHas('couples', [
            'id'      => $oldUserCouple->id,
            'wife_id' => $replacementUser->id,
        ]);
    }

    /** @test */
    public function manager_can_delete_a_user_the_replace_couples_manager_id()
    {
        $manager = $this->loginAsUser();
        $oldUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);
        $oldCoupleManagedCouple = Couple::factory()->create([
            'manager_id' => $oldUser->id,
        ]);
        $replacementUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $oldUser->id,
        ]);

        $this->assertDatabaseMissing('couples', [
            'manager_id' => $oldUser->id,
        ]);

        $this->assertDatabaseHas('couples', [
            'id'         => $oldCoupleManagedCouple->id,
            'manager_id' => $replacementUser->id,
        ]);
    }

    /** @test */
    public function user_replacement_options_only_available_on_same_gender()
    {
        $manager = $this->loginAsUser();
        $maleUser = User::factory()->male()->create([
            'manager_id' => $manager->id,
        ]);
        $maleUserChild = User::factory()->create(['father_id' => $maleUser->id]);

        $replacementMaleUser = User::factory()->male()->create();
        $femaleUser = User::factory()->female()->create();

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$maleUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));
        $response->assertSee($replacementMaleUser->name);
        $response->assertDontSee($femaleUser->name);
    }

    /** @test */
    public function bugfix_handle_duplicated_couple_on_user_deletion()
    {
        $manager = $this->loginAsUser();
        $singleWife = User::factory()->female()->create(['manager_id' => $manager->id]);
        $oldUser = User::factory()->male()->create(['manager_id' => $manager->id]);
        $replacementUser = User::factory()->male()->create(['manager_id' => $manager->id]);
        $oldUserCouple = Couple::factory()->create([
            'husband_id' => $oldUser->id,
            'wife_id'    => $singleWife->id,
        ]);
        $duplicatedCouple = Couple::factory()->create([
            'husband_id' => $replacementUser->id,
            'wife_id'    => $singleWife->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', ['id' => $oldUser->id]);

        $this->assertDatabaseMissing('couples', ['husband_id' => $oldUser->id]);

        $this->assertDatabaseHas('couples', [
            'id'         => $oldUserCouple->id,
            'husband_id' => $replacementUser->id,
            'wife_id'    => $singleWife->id,
        ]);
    }

    /** @test */
    public function bugfix_handle_duplicated_couple_on_user_deletion_with_different_marriages()
    {
        $manager = $this->loginAsUser();
        $oldUserWife = User::factory()->female()->create(['manager_id' => $manager->id]);
        $replacementUserWife = User::factory()->female()->create(['manager_id' => $manager->id]);
        $oldUser = User::factory()->male()->create(['manager_id' => $manager->id]);
        $replacementUser = User::factory()->male()->create(['manager_id' => $manager->id]);
        $oldUserCouple = Couple::factory()->create([
            'husband_id' => $oldUser->id,
            'wife_id'    => $oldUserWife->id,
        ]);
        $newUserCouple = Couple::factory()->create([
            'husband_id' => $replacementUser->id,
            'wife_id'    => $replacementUserWife->id,
        ]);

        // Visit delete confirmation page
        $response = $this->get(route('users.edit', [$oldUser, 'action' => 'delete']));
        $response->assertStatus(200);
        $response->assertSee(__('user.replace_delete_text'));

        // Submit replace delete form
        $response = $this->delete(route('users.destroy', $oldUser), [
            'replace_delete_button' => true,
            'replacement_user_id' => $replacementUser->id,
        ]);

        $this->assertDatabaseMissing('users', ['id' => $oldUser->id]);

        $this->assertDatabaseMissing('couples', ['husband_id' => $oldUser->id]);

        $this->assertDatabaseHas('couples', [
            'id'         => $oldUserCouple->id,
            'husband_id' => $replacementUser->id,
            'wife_id'    => $oldUserWife->id,
        ]);

        $this->assertDatabaseHas('couples', [
            'id'         => $newUserCouple->id,
            'husband_id' => $replacementUser->id,
            'wife_id'    => $replacementUserWife->id,
        ]);
    }
}