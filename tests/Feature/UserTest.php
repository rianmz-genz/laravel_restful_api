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
    public function testRegisterFailed()
    {
    }
    public function testRegisterUsernameAlreadyExists()
    {
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
}
