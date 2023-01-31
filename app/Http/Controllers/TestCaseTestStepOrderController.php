<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\TestCaseTestStepOrder;
use App\Http\Controllers\TestCaseController;

class TestCaseTestStepOrderController extends Controller
{
    public function postStepOrder(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'testSteps' => 'required|array|min:2|max:2',
            'testSteps.*.testStepId' => 'required|integer|exists:test_steps,testStepId',
            'testSteps.*.order' => 'required|integer'
            ]);

        try {
            $testStepIdOne = $request['testSteps'][0]['testStepId'];
            $testStepIdTwo = $request['testSteps'][1]['testStepId'];

            TestCaseTestStepOrder::where('testCaseId','=',$request['testCaseId'])
                ->where(function($query) use ($testStepIdOne, $testStepIdTwo)
                {     
                $query->where('test_case_test_step_orders.testStepId','=',$testStepIdOne)
                        ->orWhere('test_case_test_step_orders.testStepId','=',$testStepIdTwo); 
                })->delete();

            // Saving the first step order.
            $testStepOrderOne = new TestCaseTestStepOrder();
            $testStepOrderOne['testCaseId'] = $request['testCaseId'];
            $testStepOrderOne['testStepId'] = $request['testSteps'][0]['testStepId'];
            $testStepOrderOne['order'] = $request['testSteps'][0]['order'];
            $testStepOrderOne->save();

            // Saving the second step order.
            $testStepOrderTwo = new TestCaseTestStepOrder();
            $testStepOrderTwo['testCaseId'] = $request['testCaseId'];
            $testStepOrderTwo['testStepId'] = $request['testSteps'][1]['testStepId'];
            $testStepOrderTwo['order'] = $request['testSteps'][1]['order'];
            $testStepOrderTwo->save();    
            
            // Pull the test case object with all children for return.
            $testCase = TestCaseController::getTestCaseDetailsById($request['testCaseId']);

            return response()->
            json(['result' => $testCase], 200);
                            
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to change step order.']], 500
                );        
        }
    }
}
