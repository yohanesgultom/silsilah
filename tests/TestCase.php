<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://localhost';

    protected function loginAsUser($overrides = [])
    {
        $userId = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $overrides = array_merge([
            'id'         => $userId,
            'manager_id' => $userId,
            'password'  => bcrypt('secret'),
        ], $overrides);

        $user = User::factory()->create($overrides);
        $this->actingAs($user);

        return $user;
    }
}