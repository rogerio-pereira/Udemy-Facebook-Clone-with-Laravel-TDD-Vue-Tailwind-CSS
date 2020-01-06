<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Exceptions\ValidationErrorException;
use App\User;
use App\Friend;
use App\Http\Resources\Friend as ResourcesFriend;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FriendRequestController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'friend_id' => 'required',
        ]);

        try {
            User::findOrFail($data['friend_id'])
                ->friends()->syncWithoutDetaching(auth()->user());
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
