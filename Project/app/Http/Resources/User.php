<?php

namespace App\Http\Resources;

use App\Friend;
use App\Http\Resources\Friend as ResourcesFriend;
use App\Http\Resources\UserImage as ResourceUserImage;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'users',
                'user_id' => $this->id,
                'attributes' => [
                    'name' => $this->name,
                    'friendship' => new ResourcesFriend(Friend::friendship($this->id)),
                    'cover_image' => new ResourceUserImage($this->coverImage),
                    'profile_image' => new ResourceUserImage($this->profileImage),
                ]
            ],
            'links' => [
                'self' => url('/users/'.$this->id),
            ]
        ];
    }
}
