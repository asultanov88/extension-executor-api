<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestStep;
use Carbon\Carbon;

class TestStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedTestSteps();
    }

    private function seedTestSteps(){
        TestStep::truncate();
        $testSteps=[
            [
                'testStepId'=>1,
                'description'=>'Step 1 - TestCase 1 - test description',
                'expected'=>'Step 1 - TestCase 1 - test expected',
                'testCaseId'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'testStepId'=>2,
                'description'=>'Step 2 - TestCase 1 - test description',
                'expected'=>'Step 2 - TestCase 1 - test expected',
                'testCaseId'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'testStepId'=>3,
                'description'=>'Step 1 - TestCase 2 - test description',
                'expected'=>'Step 1 - TestCase 2 - test expected',
                'testCaseId'=>2,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        TestStep::insert($testSteps);
    }
}
