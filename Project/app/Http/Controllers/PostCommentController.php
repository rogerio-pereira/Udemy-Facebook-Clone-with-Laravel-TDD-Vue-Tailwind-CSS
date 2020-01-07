<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Post;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    public function store(Post $post)
    {
        $data = request()->validate([
            'body' => ''
        ]);

        $post->comments()->create(array_merge($data, [
            'user_id' => auth()->user()->id,
        ]));

        return new CommentCollection($post->comments);
    }
}
