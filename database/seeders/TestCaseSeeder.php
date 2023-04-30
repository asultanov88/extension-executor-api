<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestCase;
use Carbon\Carbon;

class TestCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedTestCases();
    }
    private function seedTestCases(){
        TestCase::truncate();
        $testCases = [
            [
                'testCaseId'=>1,
                'title'=>'Test case 1',
                'createdBy'=>1,
                'lastUpdatedBy'=>1,
                'deleted'=>0,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'testCaseId'=>2,
                'title'=>'Test case 2',
                'createdBy'=>1,
                'lastUpdatedBy'=>1,
                'deleted'=>0,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]
        ];

        TestCase::insert($testCases);
    }
}
