<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedPermissions();
    }

    private function seedPermissions(){
        Permission::truncate();
        $permissions = [
            [
                'permissionId' => 1,
                'name' => 'Devuser',
                'description' => 'Developer access to create admin users',
            ],
            [
                'permissionId' => 2,
                'name' => 'Administrator',
                'description' => 'Administrator access - grants absolute access',
            ],
            [
                'permissionId' => 3,
                'name' => 'Create test event',
                'description' => 'Create test events',
            ],
            [
                'permissionId' => 4,
                'name' => 'Write test case',
                'description' => 'Write test cases',
            ],
            [
                'permissionId' => 5,
                'name' => 'Execute test case',
                'description' => 'Execute test cases',
            ],
        ];
        Permission::insert($permissions);
    }
}
