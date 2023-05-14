<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Directory;

class DirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedDirectories();
    }

    private function seedDirectories(){
        Directory::truncate();
        $directories=[
            [
                'directoryId'=>1,
                'name'=>'Test Project 1',
                'isProject'=>1,
                'parentDirectoryId'=>null,
                'projectId'=>null,
            ],
            [
                'directoryId'=>2,
                'name'=>'Test Directory 1',
                'isProject'=>0,
                'parentDirectoryId'=>1,
                'projectId'=>1,
            ], 
            [
                'directoryId'=>3,
                'name'=>'Test Project 2',
                'isProject'=>1,
                'parentDirectoryId'=>null,
                'projectId'=>null,
            ],
            [
                'directoryId'=>4,
                'name'=>'Test Directory 2',
                'isProject'=>0,
                'parentDirectoryId'=>3,
                'projectId'=>3,

            ],
            [
                'directoryId'=>5,
                'name'=>'Test Sub Directory 2',
                'isProject'=>0,
                'parentDirectoryId'=>4,
                'projectId'=>3,
            ],
            [
                'directoryId'=>6,
                'name'=>'Test Sub Directory 3',
                'isProject'=>0,
                'parentDirectoryId'=>5,
                'projectId'=>3,
            ],
            [
                'directoryId'=>7,
                'name'=>'Test Blank Project 3',
                'isProject'=>1,
                'parentDirectoryId'=>null,
                'projectId'=>null,
            ],
        ];

        Directory::insert($directories);
    }
}
