<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess()
    {
        $this->post('api/users', [
            'username' => 'rianmz',
            'password' => 'password',
            'name' => 'Adrian Aji Septa'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'username' => 'rianmz',
                    'name' => 'Adrian Aji Septa'
                ]
            ]);
    }
    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get('api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'test',
                'name' => 'test'
            ]

        ]);
    }
    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first();
        $this->patch('api/users/current', [
            'name' => 'anyar'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200);
        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }
    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'username' => 'test',
                    'name' => 'test',
                ]
            ]);
        $user = User::where('username', 'test')->first();
        assertNotNull($user->token);
    }
    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->delete('api/users/logout', [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }
}
