<?php

namespace Tests\Feature;

use App\User;
use App\UserImage;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserImagesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * @test
     */
    public function imagesCanBeUploaded()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $file = UploadedFile::fake()->image('user-image.jpg');

        $response = $this->post('/api/userImages', [
                'image' => $file,
                'width' => 850,
                'height' => 300,
                'location' => 'cover',
            ])
            ->assertStatus(201);

        Storage::disk('public')->assertExists('user-images/'.$file->hashName());
        $userImage = UserImage::first();
        $this->assertEquals('user-images/'.$file->hashName(), $userImage->path);
        $this->assertEquals('850', $userImage->width);
        $this->assertEquals('300', $userImage->height);
        $this->assertEquals('cover', $userImage->location);
        $this->assertEquals($user->id, $userImage->user_id);
        $response->assertJson([
            'data' => [
                'type' => 'user-images',
                'user_image_id' => $userImage->id,
                'attributes' => [
                    'path' => url($userImage->path),
                    'width' => $userImage->width,
                    'height' => $userImage->height,
                    'location' => $userImage->location,
                ]
            ],
            'links' => [
                'self' => url('users/'.$user->id),
            ]
        ]);
    }
}
