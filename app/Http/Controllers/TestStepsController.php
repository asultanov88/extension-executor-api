<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestStep;
use App\Models\TestCaseTestStepOrder;
use App\Models\TestCase;
use App\Http\Controllers\TestCaseController;
use Exception;

class TestStepsController extends Controller
{
    public function postTestStep(Request $request){
        $request->validate([
            'description'=>'required',
            'expected'=>'required',
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            ]);

        try {         
            // Save the new test step. 
            $testStep = new TestStep();
            $testStep['description'] = $request['description'];
            $testStep['expected'] = $request['expected'];
            $testStep['testCaseId'] = $request['testCaseId'];
            $testStep->save();
            $newTestStepId = $testStep->testStepId;

            // Get the last test step order previously created for the test case.
            $testStepLastOrder = TestCaseTestStepOrder::where('testCaseId','=',$request['testCaseId'])
                                ->orderBy('test_case_test_step_orders.order','DESC')
                                ->first();

            // Insert the new step order number for the new test step. The last order number is incremented.
            $newTestCaseTestStepOrder = new TestCaseTestStepOrder();
            $newTestCaseTestStepOrder['testCaseId'] = $request['testCaseId'];
            $newTestCaseTestStepOrder['testStepId'] = $newTestStepId;
            $newTestCaseTestStepOrder['order'] = $testStepLastOrder ? (int)$testStepLastOrder['order'] + 1 : 1;
            $newTestCaseTestStepOrder->save();

            // Pull the test case object with all children for return.
            $testCase = TestCaseController::getTestCaseDetailsById($request['testCaseId']);
            
            return response()->
            json(['result' => $testCase], 200);
            
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create test step.']], 500
              );        
        }
    }
}
