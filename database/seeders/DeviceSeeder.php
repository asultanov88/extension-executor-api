<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedDevices();
    }
    
    private function seedDevices(){
        Device::truncate();
        $devices = [
            [
                'deviceId'=>1,
                'name'=>'Desktop 1920 X 1080',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,  
            ],
            [
                'deviceId'=>2,
                'name'=>'Desktop 1440 X 900',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>3,
                'name'=>'Desktop 1366 X 768',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>4,
                'name'=>'Desktop 1280 X 960',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>5,
                'name'=>'Desktop 800 X 600',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>6,
                'name'=>'iPhone SE',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>7,
                'name'=>'iPhone XR',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>8,
                'name'=>'iPhone 12 Pro',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>9,
                'name'=>'Pixel 5',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>10,
                'name'=>'Samsung Galaxy S20',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
            [
                'deviceId'=>11,
                'name'=>'iPad mini',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
            ],
        ];

        Device::insert($devices);
    }
}
