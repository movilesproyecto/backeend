<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Department;
use App\Models\User;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadTest extends TestCase
{
    protected $user;
    protected $department;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->department = Department::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function can_upload_single_image()
    {
        $file = UploadedFile::fake()->image('department.jpg', 640, 480);

        $response = $this->actingAs($this->user)->postJson(
            "/api/departments/{$this->department->id}/images",
            [
                'images' => [$file]
            ]
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'uploaded',
            'images' => [
                '*' => ['id', 'fileName', 'url', 'fileSize', 'mimeType', 'isPrimary']
            ]
        ]);

        $this->assertDatabaseHas('images', [
            'department_id' => $this->department->id,
            'file_name' => 'department.jpg',
        ]);
    }

    /** @test */
    public function can_upload_multiple_images()
    {
        $files = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.jpg'),
            UploadedFile::fake()->image('image3.jpg'),
        ];

        $response = $this->actingAs($this->user)->postJson(
            "/api/departments/{$this->department->id}/images",
            [
                'images' => $files
            ]
        );

        $response->assertStatus(201);
        $response->assertJson(['uploaded' => 3]);
        $this->assertEquals(3, $this->department->images()->count());
    }

    /** @test */
    public function can_get_department_images()
    {
        Image::create([
            'department_id' => $this->department->id,
            'file_path' => 'departments/1/test.jpg',
            'file_name' => 'test.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
            'uploaded_by' => $this->user->id,
            'is_primary' => true,
        ]);

        $response = $this->getJson("/api/departments/{$this->department->id}/images");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function can_get_primary_image()
    {
        Image::create([
            'department_id' => $this->department->id,
            'file_path' => 'departments/1/primary.jpg',
            'file_name' => 'primary.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
            'uploaded_by' => $this->user->id,
            'is_primary' => true,
        ]);

        $response = $this->getJson("/api/departments/{$this->department->id}/images/primary");

        $response->assertStatus(200);
        $response->assertJson([
            'fileName' => 'primary.jpg'
        ]);
    }

    /** @test */
    public function can_delete_image()
    {
        $image = Image::create([
            'department_id' => $this->department->id,
            'file_path' => 'departments/1/delete.jpg',
            'file_name' => 'delete.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
            'uploaded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            "/api/departments/{$this->department->id}/images/{$image->id}"
        );

        $response->assertStatus(200);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    /** @test */
    public function can_set_primary_image()
    {
        $image = Image::create([
            'department_id' => $this->department->id,
            'file_path' => 'departments/1/test.jpg',
            'file_name' => 'test.jpg',
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
            'uploaded_by' => $this->user->id,
            'is_primary' => false,
        ]);

        $response = $this->actingAs($this->user)->putJson(
            "/api/departments/{$this->department->id}/images/{$image->id}",
            ['is_primary' => true]
        );

        $response->assertStatus(200);
        $this->assertTrue($image->fresh()->is_primary);
    }

    /** @test */
    public function cannot_upload_oversized_file()
    {
        $file = UploadedFile::fake()->create('large.jpg', 6000); // 6MB

        $response = $this->actingAs($this->user)->postJson(
            "/api/departments/{$this->department->id}/images",
            ['images' => [$file]]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function unauthorized_user_cannot_upload()
    {
        $otherUser = User::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($otherUser)->postJson(
            "/api/departments/{$this->department->id}/images",
            ['images' => [$file]]
        );

        $response->assertStatus(403);
    }
}
