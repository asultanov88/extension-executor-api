<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestCaseTestStepOrder;
use Carbon\Carbon;

class TestCaseTestStepOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedTestCaseTestStepOrder();
    }

    private function seedTestCaseTestStepOrder(){
        TestCaseTestStepOrder::truncate();
        $testCaseTestStepOrders=[
            [
                'testCaseId'=>1,
                'testStepId'=>1,
                'order'=>1
            ],
            [
                'testCaseId'=>1,
                'testStepId'=>2,
                'order'=>2
            ],
            [
                'testCaseId'=>2,
                'testStepId'=>3,
                'order'=>1
            ],
        ];

        TestCaseTestStepOrder::insert($testCaseTestStepOrders);
    }
}
