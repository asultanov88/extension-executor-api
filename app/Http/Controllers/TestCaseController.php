<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestCase;
use App\Models\TestCaseTestStepOrder;
use Exception;

class TestCaseController extends Controller
{
    public function getTestCaseSearch(Request $request){
        $request->validate([
            'title'=>'required|min:2|max:500',
            'includeDeleted'=>'required|boolean',
        ]);

        try {
            $result = null;
            if(!$request['includeDeleted']){
                $result = TestCase::where('title','like', '%'.$request['title'].'%')
                    ->where('deleted','=','0')
                    ->limit(10)
                    ->orderBy('title','asc')
                    ->get();
            }else{
                $result = TestCase::where('title','like', '%'.$request['title'].'%')
                ->limit(10)
                ->orderBy('title','asc')
                ->get();
            }

            return response()->
            json(['result' => $result], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to update test case.']], 500
              );        
        }
    }

    public function patchTestCase(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'title'=>'required|max:500',
        ]);

        try {
            $testCase = TestCase::where('testCaseId','=',$request['testCaseId'])->first();
            $testCase->update([
                'title' => $request['title']
            ]);

            $updatedTestCase = $this->getTestCaseDetailsById($request['testCaseId']);

            return response()->
            json(['result' => $updatedTestCase], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to update test case.']], 500
              );        
        }
    }

    public function postTestCase(Request $request){
        $request->validate([
            'title'=>'required|max:500',
        ]);
        try {
            $testCase = new TestCase();
            $testCase['title'] = $request['title'];
            $testCase->save();
            $newTestCase = TestCase::where('testCaseId','=',$testCase->testCaseId)->first();
            
            return response()->
            json(['result' => $newTestCase], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create project.']], 500
              );        
        }
    }

    public function getTestCase(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
        ]);

        try {
            $result = $this->getTestCaseDetailsById($request['testCaseId']);
            return response()->
            json(['result' => $result], 200);
            
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create project.']], 500
              );        
        }
    }

    /**
     * Gets test case with nested objects by test case id.
     */
    public function getTestCaseDetailsById($testCaseId){
        $testCase = TestCase::where('testCaseId','=',$testCaseId)->first();
        $modifiedTestStepOrders = TestCaseTestStepOrder::with('testStep')
                                ->where('testCaseId','=',$testCaseId)
                                ->orderBy('test_case_test_step_orders.order','ASC')
                                ->get(
                                    [
                                        'testStepId',
                                        'order'
                                    ]
                                )
                                ->toArray();
        $testCase['testStepOrder'] = $modifiedTestStepOrders; 
        return $testCase;
    }
}
