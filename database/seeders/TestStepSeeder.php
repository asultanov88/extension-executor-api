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
                'TestStepId'=>1,
                'Description'=>'Step 1 - TestCase 1 - test description',
                'Expected'=>'Step 1 - TestCase 1 - test expected',
                'TestCaseId'=>1,
                'Deleted'=>0,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'TestStepId'=>2,
                'Description'=>'Step 2 - TestCase 1 - test description',
                'Expected'=>'Step 2 - TestCase 1 - test expected',
                'TestCaseId'=>1,
                'Deleted'=>0,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'TestStepId'=>3,
                'Description'=>'Step 1 - TestCase 2 - test description',
                'Expected'=>'Step 1 - TestCase 2 - test expected',
                'TestCaseId'=>2,
                'Deleted'=>0,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        TestStep::insert($testSteps);
    }
}
