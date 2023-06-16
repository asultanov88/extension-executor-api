<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Event;

class ProductController extends Controller
{
    /**
     * Gets product list.
     */
    public function getProductList(Request $request){
        try {
           
            $productList = Product::where('deleted','=',0)->get();
            return response()->json(['result' => $productList], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get product list']], 500
              );        
        }
    }

    /**
     * Gets product by id.
     */
    public function getProduct(Request $request){
        $request->validate([
            'productId'=>'required|integer|exists:products,productId',
        ]);

        try {
           
            // Only active products are returned.
            $product = Product::where('productId','=',$request['productId'])->first();
            return response()->json(['result' => $product], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get product details']], 500
              );        
        }
    }

    /**
     * Deletes product.
     */
    public function deleteProduct(Request $request){
        $request->validate([
            'productId'=>'required|integer|exists:products,productId',
        ]);

        try {
           
            $product = Product::where('productId','=',$request['productId'])->first();
            $product->update([
                'deleted' => 1,
                'updatedBy' => $request->user['userProfileId'],
            ]);
            return response()->json(['result' => ['message' => 'success']], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete product']], 500
              );        
        }
    }

    /**
     * Edits existing product.
     */
    public function patchProduct(Request $request){
        $request->validate([
            'productId'=>'required|integer|exists:products,productId',
            'name'=>'required|min:2|max:100',
            'description'=>'max:500',
        ]);

        // Trim possible whitespaces.
        $request['name'] = trim($request['name']);
        $request['description'] = trim($request['description']);

        // Product name must be unique.
        if(!is_null(Product::where('name','=',$request['name'])->where('productId','!=',$request['productId'])->where('deleted','=',0)->first())){
            return response()->json(
                ['result' => ['message' => 'Product name already exists']], 500
                );
        }
        
        try {
           
            $product = Product::where('productId','=',$request['productId'])->first();
            $product->update([
                'name' => $request['name'],
                'description' => $request['description'],
                'updatedBy' => $request->user['userProfileId'],
            ]);
            $product->refresh();

            return response()->json(['result' => $product], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to update product']], 500
              );        
        }
    }

    /**
     * Posts a new product.
     */
    public function postProduct(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:100',
            'description'=>'max:500',
        ]);
        
        // Trim possible whitespaces.
        $request['name'] = trim($request['name']);
        $request['description'] = trim($request['description']);

        // Product name must be unique.
        if(!is_null(Product::where('name','=',$request['name'])->where('deleted','=',0)->first())){
            return response()->json(
                ['result' => ['message' => 'Product name must be unique']], 500
              );
        }

        try {
           
            $product = new Product();
            $product['name'] = $request['name'];
            $product['description'] = $request['description'];
            $product['deleted'] = 0;
            $product['createdBy'] = $request->user['userProfileId'];
            $product['updatedBy'] = $request->user['userProfileId'];
            $product->save();

            $product->refresh();

            return response()->json(['result' => $product], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create product']], 500
              );        
        }
    }
}
