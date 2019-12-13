<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\PostCollection;

class UserPostController extends Controller
{
    public function index(User $user)
    {
        return new PostCollection($user->posts);
    }
}
