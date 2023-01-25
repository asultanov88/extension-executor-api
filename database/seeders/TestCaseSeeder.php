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
                'TestCaseId'=>1,
                'Title'=>'Test case 1',
                'CreatedBy'=>1,
                'Deleted'=>0,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'TestCaseId'=>2,
                'Title'=>'Test case 2',
                'CreatedBy'=>2,
                'Deleted'=>0,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]
        ];

        TestCase::insert($testCases);
    }
}
