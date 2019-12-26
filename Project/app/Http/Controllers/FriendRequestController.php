<?php

namespace App\Http\Controllers;

use App\User;
use App\Friend;
use App\Http\Resources\Friend as ResourcesFriend;
use Illuminate\Http\Request;

class FriendRequestController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'friend_id' => '',
        ]);

        User::find($data['friend_id'])
            ->friends()->attach(auth()->user());

        return new ResourcesFriend(
            Friend::where('user_id', auth()->user()->id)
                ->where('friend_id', $data['friend_id'])
                ->first()
        );
    }
}
