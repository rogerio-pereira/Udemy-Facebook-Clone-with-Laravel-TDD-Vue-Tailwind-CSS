<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\User;
use App\Friend;
use App\Http\Resources\Friend as ResourcesFriend;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FriendRequestController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'friend_id' => '',
        ]);

        try {
            User::findOrFail($data['friend_id'])
                ->friends()->attach(auth()->user());
        }
        catch (ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }

        return new ResourcesFriend(
            Friend::where('user_id', auth()->user()->id)
                ->where('friend_id', $data['friend_id'])
                ->first()
        );
    }
}
