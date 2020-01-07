<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostCommentsTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @test
     */
    public function aUserCanCommentOnAPost()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123]);

        $response = $this->post('/api/posts/'.$post->id.'/comment', [
                            'body' => 'A great comment here.'
                        ])
                        ->assertStatus(200);
        
        $comment = Comment::first();

        $this->assertCount(1, Comment::all());
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals('A great comment here.', $comment->body);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comment',
                        'comment_id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'data' => [
                                    'user_id' => $user->id,
                                    'attributes' => [
                                        'name' => $user->name,
                                    ]
                                ]
                            ],
                            'body' => 'A great comment here.',
                            'comment_at' => $comment->created_at->diffForHumans(),
                        ],
                    ],
                    'links' => [
                        'self' => url('/posts/123'),
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts'),
            ]
        ]);
    }

    /**
     * @test
     */
    public function aBodyIsRequiredToLeaveACommentOnAPost()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123]);

        $response = $this->post('/api/posts/'.$post->id.'/comment', [
                            'body' => ''
                        ])
                        ->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('body', $responseString['errors']['meta']);
    }

    /**
     * @test
     */
    public function postAreReturnedWithComments()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123, 'user_id' => $user->id]);
        $response = $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => 'A great comment here.'
        ]);

        $response = $this->get('/api/posts');

        $comment = Comment::first();
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'comments' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => 'comment',
                                                'comment_id' => 1,
                                                'attributes' => [
                                                    'commented_by' => [
                                                        'data' => [
                                                            'user_id' => $user->id,
                                                            'attributes' => [
                                                                'name' => $user->name,
                                                            ]
                                                        ]
                                                    ],
                                                    'body' => 'A great comment here.',
                                                    'comment_at' => $comment->created_at->diffForHumans(),
                                                ],
                                            ],
                                            'links' => [
                                                'self' => url('/posts/123'),
                                            ]
                                        ]
                                    ],
                                    'comment_count' => 1,
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
