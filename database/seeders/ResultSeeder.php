<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Result;

class ResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedResults();
    }
    private function seedResults(){
        Result::truncate();
        $results = [
            [
                'statusId'=>1,
                'description'=>'Pass',
            ],
            [
                'statusId'=>2,
                'description'=>'Fail',
            ],
            [
                'statusId'=>3,
                'description'=>'Skip',
            ],
        ];

        Result::insert($results);
    }
}
