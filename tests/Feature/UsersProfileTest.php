<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UsersProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_search_users_profile()
    {
        $jono = User::factory()->create(['name' => 'Jono']);
        $jeni = User::factory()->create(['name' => 'Jeni']);
        $johan = User::factory()->create(['name' => 'Johan']);

        // Search route requires authentication; guest gets redirected to login.
        $response = $this->get(route('users.search', ['q' => 'jo']));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_view_other_users_profile()
    {
        $user = $this->loginAsUser();
        $user = User::factory()->create();

        $response = $this->get(route('users.show', $user->id));
        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /** @test */
    public function user_will_see_edit_profile_if_an_invalid_tab_selected()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'invalid_tab']));
        $response->assertStatus(200);
        $response->assertSee('input', ['name' => 'nickname']);
        $response->assertSee('input', ['name' => 'name']);
    }

    /** @test */
    public function user_can_edit_profile()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', $user->id));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'nickname'    => 'Nama Panggilan',
            'name'        => 'Nama User',
            'gender_id'   => 1,
            'dob'         => '1959-06-09',
            'yob'         => '',
            'birth_order' => 3,
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertDatabaseHas('users', [
            'id'          => $user->id,
            'nickname'    => 'Nama Panggilan',
            'name'        => 'Nama User',
            'gender_id'   => 1,
            'dob'         => '1959-06-09',
            'yob'         => '1959',
            'birth_order' => 3,
        ]);
    }

    /** @test */
    public function user_can_update_yob_only()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', $user->id));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'dob' => '',
            'yob' => '2003',
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertDatabaseHas('users', [
            'id'  => $user->id,
            'dob' => null,
            'yob' => '2003',
        ]);
    }

    /** @test */
    public function user_can_edit_contact_address()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'contact_address']));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'address' => 'Jln. Angkasa, No. 70',
            'city'    => 'Nama Kota',
            'phone'   => '081234567890',
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertDatabaseHas('users', [
            'id'      => $user->id,
            'address' => 'Jln. Angkasa, No. 70',
            'city'    => 'Nama Kota',
            'phone'   => '081234567890',
        ]);
    }

    /** @test */
    public function user_can_edit_login_account()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'login_account']));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'email'    => '',
            'password' => '',
        ]);

        $user = $user->fresh();

        // Empty email clears the email; empty password must not replace the existing one.
        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'email' => null,
        ]);
        $this->assertTrue(app('hash')->check('secret', $user->password));
    }

    /** @test */
    public function user_can_edit_death()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'death']));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'dod' => '2003-10-17',
            'yod' => '',
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertDatabaseHas('users', [
            'id'  => $user->id,
            'dod' => '2003-10-17',
            'yod' => '2003',
        ]);
    }

    /** @test */
    public function user_can_update_yod_only()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'death']));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'dod' => '',
            'yod' => '2003',
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertDatabaseHas('users', [
            'id'  => $user->id,
            'dod' => null,
            'yod' => '2003',
        ]);
    }

    /** @test */
    public function user_can_update_died_person_cemetary_location()
    {
        $user = $this->loginAsUser();

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'death']));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'dod'                         => '',
            'yod'                         => '2003',
            'cemetery_location_name'      => 'Some name',
            'cemetery_location_address'   => 'Some address',
            'cemetery_location_latitude'  => '-3.333333',
            'cemetery_location_longitude' => '114.583333',
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertDatabaseHas('users', [
            'id'  => $user->id,
            'dod' => null,
            'yod' => '2003',
        ]);

        $this->assertDatabaseHas('user_metadata', [
            'user_id' => $user->id,
            'key'     => 'cemetery_location_name',
            'value'   => 'Some name',
        ]);

        $this->assertDatabaseHas('user_metadata', [
            'user_id' => $user->id,
            'key'     => 'cemetery_location_address',
            'value'   => 'Some address',
        ]);

        $this->assertDatabaseHas('user_metadata', [
            'user_id' => $user->id,
            'key'     => 'cemetery_location_latitude',
            'value'   => '-3.333333',
        ]);

        $this->assertDatabaseHas('user_metadata', [
            'user_id' => $user->id,
            'key'     => 'cemetery_location_longitude',
            'value'   => '114.583333',
        ]);
    }

    /** @test */
    public function user_metadata_can_be_prefilled_on_the_edit_form()
    {
        $user = $this->loginAsUser();
        $user->metadata()->create([
            'key' => 'cemetery_location_name',
            'value' => 'Some place name',
        ]);

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'death']));
        $response->assertStatus(200);
        $response->assertSee('input', ['name' => 'cemetery_location_name', 'value' => 'Some place name']);
    }

    /** @test */
    public function manager_can_add_login_account_on_a_user()
    {
        $manager = $this->loginAsUser();
        $user = User::factory()->create(['manager_id' => $manager->id]);

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'login_account']));
        $response->assertStatus(200);

        $response = $this->patch(route('users.update', $user->id), [
            'email'    => 'user@mail.com',
            'password' => 'Secr3t',
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertEquals('user@mail.com', $user->email);
        $this->assertTrue(app('hash')->check('Secr3t', $user->password));
    }

    /** @test */
    public function manager_can_add_user_email_without_a_password()
    {
        $manager = $this->loginAsUser();
        $user = User::factory()->create(['manager_id' => $manager->id]);

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'login_account']));
        $response->assertStatus(200);
        $response->assertSee(route('users.edit', [$user->id, 'tab' => 'login_account']));

        $response = $this->patch(route('users.update', $user->id), [
            'email'    => 'user@mail.com',
            'password' => '',
        ]);

        $response->assertRedirect(route('users.show', $user->id));

        $user = $user->fresh();

        $this->assertEquals('user@mail.com', $user->email);
        $this->assertNull($user->password);
    }

    /** @test */
    public function empty_password_does_not_replace_existing()
    {
        $manager = $this->loginAsUser();
        $user = User::factory()->create([
            'manager_id' => $manager->id,
            'password'   => 'some random string password',
        ]);

        $response = $this->get(route('users.edit', [$user->id, 'tab' => 'login_account']));
        $response->assertStatus(200);
        $response->assertSee(route('users.edit', [$user->id, 'tab' => 'login_account']));

        $response = $this->patch(route('users.update', $user->id), [
            'email'    => 'user@mail.com',
            'password' => '',
        ]);

        $user = $user->fresh();

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'manager_id' => $manager->id,
            'password'   => 'some random string password',
        ]);
    }

    /** @test */
    public function user_can_upload_their_own_photo()
    {
        Storage::fake(config('filesystems.default'));

        $user = $this->loginAsUser();
        $this->assertNull($user->photo_path);

        $response = $this->patch(route('users.photo-upload', $user->id), [
            'photo' => \Illuminate\Http\Testing\File::fake()->image('avatar.png'),
        ]);

        $user = $user->fresh();

        $this->assertNotNull($user->photo_path);
        Storage::assertExists($user->photo_path);
    }
}