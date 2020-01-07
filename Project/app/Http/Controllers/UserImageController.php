<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserImage as ResourceUserImage;
use Illuminate\Http\Request;

class UserImageController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'image' => '',
            'width' => '',
            'height' => '',
            'location' => '',
        ]);

        $image = $data['image']->store('user-images', 'public');

        $userImage = auth()->user()->images()->create([
            'path' => $image,
            'width' => $data['width'],
            'height' => $data['height'],
            'location' => $data['location'],
        ]);

        return new ResourceUserImage($userImage);
    }
}
