<?php

namespace Tests\Feature;

use App\User;
use App\Friend;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FriendsTest extends TestCase
{
    Use RefreshDatabase;

    /**
     *@test
     */
    public function aUserCanSendFriendRequest()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $response = $this->post('/api/friendRequest', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $friendRequest = Friend::first();

        $this->assertNotNull($friendRequest);
        $this->assertEquals($anotherUser->id, $friendRequest->friend_id);
        $this->assertEquals($user->id, $friendRequest->user_id);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null,
                ]
            ],
            'links' => [
                'self' => url('users/'.$anotherUser->id),
            ]
        ]);
    }

    /**
     * @test
     */
    public function onlyValidUsersCanBeFriendRequested()
    {
        //$this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/friendRequest', [
            'friend_id' => 123,
        ])->assertStatus(404);

        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'User not found',
                'detail' => 'Unable to locate the User with the given information',
            ]
        ]);
    }
    
    /**
     * @test
     */
    public function friendRequestCanBeAccepted()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $this->post('/api/friendRequest', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friendRequestResponse', [
                'user_id' => $user->id,
                'status' => 1,
            ])
            ->assertStatus(200);

        $friendRequest = Friend::first();

        $this->assertNotNull($friendRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
        $this->assertEquals(1, $friendRequest->status);
        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => $friendRequest->confirmed_at->diffForHumans(),
                    'friend_id' => $friendRequest->friend_id,
                    'user_id' => $friendRequest->user_id,
                ]
            ],
            'links' => [
                'self' => url('users/'.$anotherUser->id),
            ]
        ]);
    }

    
    /**
     * @test
     */
    public function friendRequestCanBeIgnored()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $this->post('/api/friendRequest', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($anotherUser, 'api')
            ->delete('/api/friendRequestResponse/delete', [
                'user_id' => $user->id,
            ])
            ->assertStatus(204);

        $friendRequest = Friend::first();

        $this->assertNull($friendRequest); 
        $response->assertNoContent();
    }

    /**
     * @test
     */
    public function onlyValidFriendRequestCanBeAccepted()
    {
        $anotherUser = factory(User::class)->create();

        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friendRequestResponse', [
                'user_id' => 123,
                'status' => 1,
            ])
            ->assertStatus(404);
        
        $this->assertNull(Friend::first());
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request not found',
                'detail' => 'Unable to locate the friend request with the given information',
            ]
        ]);
    }

    /**
     * @test
     */
    public function onlyTheRecipientCanAcceptFriendRequest()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $this->post('/api/friendRequest', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->post('/api/friendRequestResponse', [
                'user_id' => $user->id,
                'status' => 1,
            ])
            ->assertStatus(404);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request not found',
                'detail' => 'Unable to locate the friend request with the given information',
            ]
        ]);
    }

    /**
     * @test
     */
    public function onlyTheRecipientCanIgnoreFriendRequest()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $this->post('/api/friendRequest', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->delete('/api/friendRequestResponse/delete', [
                'user_id' => $user->id,
            ])
            ->assertStatus(404);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request not found',
                'detail' => 'Unable to locate the friend request with the given information',
            ]
        ]);
    }

    /**
     * @test
     */
    public function aFriendIdIsRequiredForFriendRequest()
    {
        $response = $this->actingAs($user = factory(User::class)->create(), 'api')
            ->post('/api/friendRequest', [
                'friend_id' => '',
            ])->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('friend_id', $responseString['errors']['meta']);
    }

    /**
     * @test
     */
    public function aUserIdAndStatusIsRequiredForFriendRequestResponses()
    {
        $response = $this->actingAs($user = factory(User::class)->create(), 'api')
            ->post('/api/friendRequestResponse', [
                'user_id' => '',
                'status' => '',
            ])
            ->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
        $this->assertArrayHasKey('status', $responseString['errors']['meta']);
    }

    /**
     * @test
     */
    public function aUserIdIsRequiredForIgnoringAFriendRequestResponses()
    {
        $response = $this->actingAs($user = factory(User::class)->create(), 'api')
            ->delete('/api/friendRequestResponse/delete', [
                'user_id' => '',
            ])
            ->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
    }

    /**
     * @test
     */
    public function aFriendshipIsRetrievedWhenFetchingTheProfile()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $friendRequest = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => true
        ]);

        $this->get('/api/users/'.$anotherUser->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function anInverseFriendshipIsRetrievedWhenFetchingTheProfile()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $friendRequest = Friend::create([
            'friend_id' => $user->id,
            'user_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => true
        ]);

        $this->get('/api/users/'.$anotherUser->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
