<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestCase;
use App\Models\TestCaseTestStepOrder;
use Exception;

class TestCaseController extends Controller
{
    public function postTestCase(Request $request){
        $request->validate([
            'title'=>'required|max:500',
        ]);
        try {
            $testCase = new TestCase();
            $testCase['Title'] = $request['title'];
            $testCase->save();
            $newTestCase = TestCase::where('TestCaseId','=',$testCase->TestCaseId)->first();
            
            return response()->
            json(['result' => $newTestCase], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create project.']], 500
              );        
        }
    }
}
