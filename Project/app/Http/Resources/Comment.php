<?php

namespace App\Http\Resources;

use App\Http\Resources\User as ResourceUser;
use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
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
                'type' => 'comment',
                'comment_id' => $this->id,
                'attributes' => [
                    'commented_by' => new ResourceUser($this->user),
                    'body' => $this->body,
                    'comment_at' => $this->created_at->diffForHumans(),
                ],
            ],
            'links' => [
                'self' => url('/posts/'.$this->post_id),
            ]
        ];
    }
}
