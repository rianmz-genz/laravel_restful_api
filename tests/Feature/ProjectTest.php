<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateProjectSuccess()
    {
        $this->seed([UserSeeder::class]);
        Storage::fake('public'); // Menggunakan penyimpanan palsu untuk menghindari penyimpanan fisik file

        $image = UploadedFile::fake()->image('test_image.jpg');

        $response = $this->post('api/projects', [
            'name' => 'New Project',
            'description' => 'Description of the project',
            'image' => $image,
        ], [
            'Authorization' => 'test'
        ]);
        // Log::info(json_encode($response));
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'image',
                ],
                'message',
                'status',
                'code'
            ]);
    }
}
