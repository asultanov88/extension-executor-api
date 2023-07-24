<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Browser;

class BrowserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedBrowsers();
    }
    private function seedBrowsers(){
        Browser::truncate();
        $browsers = [
            [
                'browserId'=>1,
                'name'=>'Chrome',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,  
            ],
            [
                'browserId'=>2,
                'name'=>'Edge',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,  
            ],
            [
                'browserId'=>3,
                'name'=>'Firefox',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,  
            ],
            [
                'browserId'=>4,
                'name'=>'Safari',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,  
            ],
            [
                'browserId'=>5,
                'name'=>'Opera',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,  
            ],
        ];
        
        Browser::insert($browsers);
    }
}