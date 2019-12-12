<?php

namespace Tests\Feature;

use App\User;
use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function AUserCanPostATextPost()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response =  $this->post('/api/posts', [
            'data' => [
                'type' => 'post',
                'attributes' => [
                    'body' => 'Testing body',
                ]
            ]
        ]);

        $post = Post::first();

        $response->assertStatus(201);
    }
}
