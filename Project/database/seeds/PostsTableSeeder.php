<?php

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
    }
}
