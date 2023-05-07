<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserProject;

class UserProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedUserProjects();
    }

    private function seedUserProjects(){
        UserProject::truncate();
        $userProjects=[
            [
                'userProjectId'=>1,
                'userProfileId'=>1,
                'projectId'=>1,
            ], 
            [
                'userProjectId'=>2,
                'userProfileId'=>1,
                'projectId'=>3,
            ], 
            [
                'userProjectId'=>3,
                'userProfileId'=>1,
                'projectId'=>7,
            ], 
        ];

        UserProject::insert($userProjects);
    }
}
