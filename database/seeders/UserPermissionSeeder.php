<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserPermission;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedUserPermissions();
    }

    private function seedUserPermissions(){
        UserPermission::truncate();
        $userPermissions = [
            [
                'userPermissionId' => 1,
                'userId' => 1,
                'permissionId' => 1,
            ],
        ];
        UserPermission::insert($userPermissions);
    }
}
