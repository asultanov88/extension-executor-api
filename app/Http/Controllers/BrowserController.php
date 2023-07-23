<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Browser;

class BrowserController extends Controller
{
    /**
     * Gets browser object by browser id.
     */
    public function getBrowser(Request $request){
        $request->validate([
            'browserId' => 'required|exists:browser,browserId',
        ]);  

        try {

            $browser = Browser::where('browserId','=',$request['browserId'])->first();
            return response()->json(['result' => $browser], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get browser.']], 500
              );        
        }
    }

    /**
     * Gets a list of all active browsers.
     */
    public function getBrowserList(Request $request){
        try {

            $browserList = Browser::where('deleted','=',0)->orderBy('name', 'ASC')->get();
            return response()->json(['result' => $browserList], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get browser list']], 500
              );        
        }
    }


    /**
     * Logical delete of browser.
     */
    public function deleteBrowser(Request $request){
        $request->validate([
            'browserId' => 'required|exists:browser,browserId',
        ]);

        try {

            $browser = Browser::where('browserId','=',$request['browserId'])->first();
            $browser->update([
                'deleted' => 1,
                'updatedBy' => $request->user['userProfileId'],
            ]);

            return response()->json(['result' => ['message' => 'success']], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete browser']], 500
              );        
        }
    }


    /**
     * Updates browser name by creating a new record and replacing the old one.
     */
    public function patchBrowser(Request $request){
        $request->validate([
            'browserId' => 'required|exists:browser,browserId',
            'name' => 'required|min:2|max:100',
        ]);

        $existingBrowser = Browser::where('name','=',$request['name'])
                            ->where('deleted','!=',1)
                            ->where('browserId','!=',$request['browserId'])
                            ->first();

        if(!is_null($existingBrowser)){
            return response()->json(
                ['result' => ['message' => 'Browser name must be unique']], 500
              );
        }

        $sameBrowser = Browser::where('browserId','=',$request['browserId'])->first();
        // If user is updating the browser to the same name, return the existing browser object.
        if(!is_null( $sameBrowser) &&  $sameBrowser['name'] == $request['name']){
            return response()->json(['result' => $sameBrowser], 200); 
        }

        try {

            $browser = new Browser();
            $browser['name'] = $request['name'];
            $browser['createdBy'] = $request->user['userProfileId'];
            $browser['updatedBy'] = $request->user['userProfileId'];
            $browser->save();
            $browser->refresh();

            $oldBrowser = Browser::where('browserId','=',$request['browserId'])->first();
            $oldBrowser->update(
                [
                    'deleted' => 1,
                    'updatedBy' => $request->user['userProfileId'],
                    'replacedByBrowserId' => $browser['browserId'],
                ]
            );

            return response()->json(['result' => $browser], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to update browser']], 500
              );        
        }        
    }


    /**
     * Creates a new browser.
     */
    public function postBrowser(Request $request){
        $request->validate([
            "name" => "required|min:2|max:100"
        ]);

        // Browser name must be unique.
        if(!is_null(Browser::where('name','like','%'.$request['name'].'%')->where('deleted','=',0)->first())){
            return response()->json(
                ['result' => ['message' => 'Browser name must be unique']], 500
                );
        }

        try {

            $browser = new Browser();
            $browser['name'] = $request['name'];
            $browser['createdBy'] = $request->user['userProfileId'];
            $browser['updatedBy'] = $request->user['userProfileId'];
            $browser->save();
            $browser->refresh();

            return response()->json(['result' => $browser], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create browser']], 500
              );        
        }
    }
}
