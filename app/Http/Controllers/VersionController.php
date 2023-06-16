<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Version;

class VersionController extends Controller
{
    /**
     * Deletes version by versionId.
     */
    public function deleteVersion(Request $request){
        $request->validate([
            'versionId'=>'required|integer|exists:versions,versionId',
        ]);

        try {
            $version = Version::where('versionId','=',$request['versionId'])->first();
            $version->update([
                'deleted' => 1,
                'updatedBy' => $request->user['userProfileId'],
            ]);            
            return response()->json(['result' => ['message' => 'success']], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete version']], 500
              );        
        } 
    }

    /**
     * Get version list for productId. 
     */
    public function getVersionList(Request $request){
        $request->validate([
            'productId'=>'required|integer|exists:products,productId',
        ]);

        try {
            $versions = Version::where('productId','=',$request['productId'])
                               ->where('deleted','=',0)
                               ->orderBy('name','ASC')->get();
            return response()->json(['result' => $versions], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get version list']], 500
              );        
        } 
    }

    /**
     * Get version details by versionId.
     */
    public function getVersion(Request $request){
        $request->validate([
            'versionId'=>'required|integer|exists:versions,versionId',
        ]);

        try {
            $version = Version::where('versionId','=',$request['versionId'])->first();
            return response()->json(['result' => $version], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get version details']], 500
              );        
        } 
    }

    /**
     * Patch existing version.
     */
    public function patchVersion(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:100',
            'productId'=>'required|integer|exists:products,productId',
            'versionId'=>'required|integer|exists:versions,versionId',
        ]);

        // Trim possible whitespaces.
        $request['name'] = trim($request['name']);

        // Version must be unique per product.
        if(!is_null(Version::where('productId','=',$request['productId'])
                           ->where('name','=',$request['name'])
                           ->where('versionId','!=',$request['versionId'])
                           ->where('deleted','=',0)
                           ->first())
        ){
            return response()->json(
                    ['result' => ['message' => 'Version already exists for this product']], 500
                );
        }
        
        try {
            $version = Version::where('versionId','=',$request['versionId'])->first();
            $version->update([
                'name' => $request['name'],
                'updatedBy' => $request->user['userProfileId'],
            ]);
            $version->refresh();

            return response()->json(['result' => $version], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create version']], 500
              );        
        }        
    }

    /**    
     * Posts new version.
     */
    public function postVersion(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:100',
            'productId'=>'required|integer|exists:products,productId',
        ]);

        // Trim possible whitespaces.
        $request['name'] = trim($request['name']);
        
        // Version must be unique per product.
        if(!is_null(Version::where('productId','=',$request['productId'])
                           ->where('name','=',$request['name'])
                           ->where('deleted','=',0)
                           ->first())
        ){
            return response()->json(
                    ['result' => ['message' => 'Version name must be unique per product']], 500
                );
        }

        try {
            $version = new Version();
            $version['name'] = $request['name'];
            $version['productId'] = $request['productId'];
            $version['deleted'] = 0;
            $version['createdBy'] = $request->user['userProfileId'];
            $version['updatedBy'] = $request->user['userProfileId'];
            $version->save();
            $version->refresh();

            return response()->json(['result' => $version], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create version']], 500
              );        
        }
    }
}
