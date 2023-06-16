<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedProducts();
    }

    private function seedProducts(){
        Product::truncate();
        $products = [
            [
                'productId'=>1,
                'name'=>'Test Product 1',
                'description'=>'Test Description 1',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'productId'=>2,
                'name'=>'Test Product 2',
                'description'=>'Test Description 2',
                'deleted'=>0,
                'createdBy'=>1,
                'updatedBy'=>1,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        Product::insert($products);
    }
}
