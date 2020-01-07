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
                    'path' => url('/storage/'.$userImage->path),
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

    /**
     * @test
     */
    public function usersAreReturnedWithTheirImages()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $file = UploadedFile::fake()->image('user-image.jpg');
        $this->post('/api/userImages', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'cover',
        ])->assertStatus(201);
        $this->post('/api/userImages', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'profile',
        ])->assertStatus(201);

        $response = $this->get('/api/users/'.$user->id);

        $response->assertJson([
            'data' => [
                'type' => 'users',
                'user_id' => $user->id,
                'attributes' => [
                    'cover_image' => [
                        'data' => [
                            'type' => 'user-images',
                            'user_image_id' => 1,
                            'attributes' => []
                        ],
                    ],
                    'profile_image' => [
                        'data' => [
                            'type' => 'user-images',
                            'user_image_id' => 2,
                            'attributes' => []
                        ],
                    ],
                ]
            ],
        ]);
    }
}
