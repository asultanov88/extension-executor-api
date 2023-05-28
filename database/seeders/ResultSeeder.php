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
                'resultId'=>1,
                'description'=>'Pass',
            ],
            [
                'resultId'=>2,
                'description'=>'Fail',
            ],
            [
                'resultId'=>3,
                'description'=>'Skip',
            ],
            [
                'resultId'=>4,
                'description'=>'Not executed',
            ],
        ];

        Result::insert($results);
    }
}
