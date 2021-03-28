<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Post::class, 10)->create();
        //factory(App\User::class, 10)->create();
         //$this->call(UsersTableSeeder::class);
    }
}
