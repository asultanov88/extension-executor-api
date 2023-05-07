<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DirectoryTestCase;

class DirectoryTestCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedDirectoryTestCases();
    }

    private function seedDirectoryTestCases(){
        DirectoryTestCase::truncate();
        $directoryTestCases=[
            [
                'directoryTestCaseId' => 1,
                'directoryId' => 2,
                'testCaseId' => 1,
            ],
            [
                'directoryTestCaseId' => 2,
                'directoryId' => 5,
                'testCaseId' => 2,
            ],
        ];
        DirectoryTestCase::insert($directoryTestCases);
    }
}
