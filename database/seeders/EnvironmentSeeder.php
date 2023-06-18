<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Environment;
use Carbon\Carbon;

class EnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedEnvironments();
    }

    private function seedEnvironments(){
        Environment::truncate();
        $environments = [
            [
                'environmentId'=>1,
                'name'=>'qa',
                'productId'=>1,
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'environmentId'=>2,
                'name'=>'intergration',
                'productId'=>1,
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        Environment::insert($environments);
    }
}
