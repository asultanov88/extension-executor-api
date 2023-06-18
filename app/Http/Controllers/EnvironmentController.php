<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Environment;

class EnvironmentController extends Controller
{
    /**
     * Deletes environment by environmentId.
     */
    public function deleteEnvironment(Request $request){
        $request->validate([
            'environmentId'=>'required|integer|exists:environments,environmentId',
        ]);

        try {
            $environment = Environment::where('environmentId','=',$request['environmentId'])->first();
            $environment->update([
                'deleted' => 1,
                'updatedBy' => $request->user['userProfileId'],
            ]);            
            return response()->json(['result' => ['message' => 'success']], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete environment']], 500
              );        
        } 
    }

    /**
     * Get environment list for productId. 
     */
    public function getEnvironmentList(Request $request){
        $request->validate([
            'productId'=>'required|integer|exists:products,productId',
        ]);

        try {
            $environments = Environment::where('productId','=',$request['productId'])
                               ->where('deleted','=',0)
                               ->orderBy('name','ASC')->get();
            return response()->json(['result' => $environments], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get environment list']], 500
              );        
        } 
    }

    /**
     * Get environment details by environmentId.
     */
    public function getEnvironment(Request $request){
        $request->validate([
            'environmentId'=>'required|integer|exists:environments,environmentId',
        ]);

        try {
            $environment = Environment::where('environmentId','=',$request['environmentId'])->first();
            return response()->json(['result' => $environment], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get environment details']], 500
              );        
        } 
    }

    /**
     * Patch existing environment.
     */
    public function patchEnvironment(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:100',
            'productId'=>'required|integer|exists:products,productId',
            'environmentId'=>'required|integer|exists:environments,environmentId',
        ]);

        // Trim possible whitespaces, force lower case.
        $request['name'] = strtolower(trim($request['name']));

        // Environment must be unique per product.
        if(!is_null(Environment::where('productId','=',$request['productId'])
                           ->where('name','=',$request['name'])
                           ->where('environmentId','!=',$request['environmentId'])
                           ->where('deleted','=',0)
                           ->first())
        ){
            return response()->json(
                    ['result' => ['message' => 'Environment already exists for this product']], 500
                );
        }
        
        try {
            $environment = Environment::where('environmentId','=',$request['environmentId'])->first();
            $environment->update([
                'name' => $request['name'],
                'updatedBy' => $request->user['userProfileId'],
            ]);
            $environment->refresh();

            return response()->json(['result' => $environment], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to update environment']], 500
              );        
        }        
    }

    /**
     * Post new environment.
     */
    public function postEnvironment(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:100',
            'productId'=>'required|integer|exists:products,productId',
        ]);

        // Trim possible whitespaces, force lower case.
        $request['name'] = strtolower(trim($request['name']));

        // Environment must be unique per product.
        if(!is_null(Environment::where('productId','=',$request['productId'])
                            ->where('name','=',$request['name'])
                            ->where('deleted','=',0)
                            ->first())
        ){
            return response()->json(
                    ['result' => ['message' => 'Environment name must be unique per product']], 500
                );
        }

        try {
            $environment = new Environment();
            $environment['name'] = $request['name'];
            $environment['productId'] = $request['productId'];
            $environment['deleted'] = 0;
            $environment['createdBy'] = $request->user['userProfileId'];
            $environment['updatedBy'] = $request->user['userProfileId'];
            $environment->save();
            $environment->refresh();

            return response()->json(['result' => $environment], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create environment']], 500
              );        
        }
    }
}
