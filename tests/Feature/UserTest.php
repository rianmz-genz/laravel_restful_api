<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess()
    {
        $this->post('api/users',[
            'username' => 'rianmz',
            'password'=> 'password',
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
}
