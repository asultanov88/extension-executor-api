<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedStatuses();
    }
    private function seedStatuses(){
        Status::truncate();
        $statuses = [
            [
                'statusId'=>1,
                'description'=>'Completed',
            ],
            [
                'statusId'=>2,
                'description'=>'In-progress',
            ],
            [
                'statusId'=>3,
                'description'=>'Cancelled',
            ],
        ];

        Status::insert($statuses);
    }
}
