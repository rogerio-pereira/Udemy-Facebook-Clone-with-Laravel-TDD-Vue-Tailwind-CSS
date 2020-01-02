<?php

namespace App\Http\Controllers;

use App\Friend;
use Illuminate\Http\Request;
use App\Http\Resources\Friend as ResourcesFriend;
use App\Exceptions\FriendRequestNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FriendRequestResponseController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'user_id' => '',
            'status' => ''
        ]);

        try {
            $friendRequest = Friend::where('user_id', $data['user_id'])
                                ->where('friend_id', auth()->user()->id)
                                ->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            throw new FriendRequestNotFoundException();
        }
        
        $friendRequest->update(array_merge($data,  [
            'confirmed_at' => now()
        ]));

        return new ResourcesFriend($friendRequest);
    }
}
