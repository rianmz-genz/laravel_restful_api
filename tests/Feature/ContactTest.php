<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => 'Adrian',
            'last_name' => 'Septa',
            'email' => 'adrian@gmail.com',
            'phone' => '088227852900'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)->assertJson([
            'data' => [
                'first_name' => 'Adrian',
                'last_name' => 'Septa',
                'email' => 'adrian@gmail.com',
                'phone' => '088227852900'
            ]
        ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'test',
                'last_name' => 'test',
                'email' => 'test@gmail.com',
                'phone' => '111111111',
            ]
        ]);
    }

    public function testSearchByName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();
        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();
        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::where('first_name', 'test')->first();
        $response = $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'testupdate',
            'last_name' => 'testupdate',
            'email' => 'testupdate@gmail.com',
            'phone' => '111111111update',
        ], [
            'Authorization' => 'test'
        ])->assertJson([
            'data' => [
                'first_name' => 'testupdate',
                'last_name' => 'testupdate',
                'email' => 'testupdate@gmail.com',
                'phone' => '111111111update',
            ]
        ]);
        self::assertNotEquals($response, $contact);
    }
}
