<?php

use App\Like;
use App\Post;
use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Post::class)->create(['user_id' => 1, 'body' => 'Text only', 'image' => null]);
        factory(Post::class)->create(['user_id' => 1, 'body' => 'Image']);

        //Create 5 posts, with content Post X, to the two users
        for($i=0; $i<=4; $i++) {
            if($i%2 == 0)
                $userId = 1;
            else
                $userId = 2;

            factory(Post::class)->create(['user_id' => $userId, 'body' => 'Post '.$i]);
        }

        factory(Like::class)->create();
    }
}
