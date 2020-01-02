<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Http\Resources\Friend as ResourcesFriend;
use Illuminate\Http\Request;

class FriendRequestResponseController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'user_id' => '',
            'status' => ''
        ]);

        $friendRequest = Friend::where('user_id', $data['user_id'])
                                ->where('friend_id', auth()->user()->id)
                                ->firstOrFail();
        
        $friendRequest->update(array_merge($data,  [
            'confirmed_at' => now()
        ]));

        return new ResourcesFriend($friendRequest);
    }
}
