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
                'TestCaseId'=>1,
                'TestStepId'=>1,
                'Order'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'TestCaseId'=>1,
                'TestStepId'=>2,
                'Order'=>2,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'TestCaseId'=>2,
                'TestStepId'=>3,
                'Order'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        TestCaseTestStepOrder::insert($testCaseTestStepOrders);
    }
}
