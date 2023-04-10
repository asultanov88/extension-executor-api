<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedUsers();
    }

    private function seedUsers(){
        User::truncate();
        $testSteps=[
            [
                'firstName'=>'TestUserFirstName',
                'lastName'=>'TestUserLastName',
                'email'=>'testUserLastName@emailtest.com',
                'password'=>'$2y$10$/yvlHuZnc2R0cMy2nRh6COwU7gB2ncC36U6mbr6zVp8IlaytSfYa.',
                'remember_token'=>null,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ] 
        ];

        User::insert($testSteps);
    }
}
