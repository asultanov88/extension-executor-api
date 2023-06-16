<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Version;
use Carbon\Carbon;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedVersions();
    }

    private function seedVersions(){
        Version::truncate();
        $versions = [
            [
                'versionId'=>1,
                'productId'=>1,
                'name'=>'1.1.1',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'versionId'=>2,
                'productId'=>1,
                'name'=>'1.1.2',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        Version::insert($versions);
    }
}
