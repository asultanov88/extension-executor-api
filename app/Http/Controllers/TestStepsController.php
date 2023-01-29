<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestStep;
use App\Models\TestCaseTestStepOrder;
use App\Models\TestCase;
use Exception;

class TestStepsController extends Controller
{
    public function postTestStep(Request $request){
        $request->validate([
            'description'=>'required',
            'expected'=>'required',
            'testCaseId'=>'required|integer|exists:test_cases,TestCaseId',
            ]);

        try {         
            // Save the new test step. 
            $testStep = new TestStep();
            $testStep['Description'] = $request['description'];
            $testStep['Expected'] = $request['expected'];
            $testStep['TestCaseId'] = $request['testCaseId'];
            $testStep->save();
            $newTestStepId = $testStep->TestStepId;

            // Get the last test step order previously created for the test case.
            $testStepLastOrder = (int)TestCaseTestStepOrder::where('TestCaseId','=',$request['testCaseId'])
                                ->orderBy('test_case_test_step_orders.Order','DESC')
                                ->first()['Order'];

            // Insert the new step order number for the new test step. The last order number is incremented.
            $newTestCaseTestStepOrder = new TestCaseTestStepOrder();
            $newTestCaseTestStepOrder['TestCaseId'] = $request['testCaseId'];
            $newTestCaseTestStepOrder['TestStepId'] = $newTestStepId;
            $newTestCaseTestStepOrder['Order'] = $testStepLastOrder+1;
            $newTestCaseTestStepOrder->save();

            // Pull the test case object with all children for return.
            $testCase = TestCase::where('TestCaseId','=',$request['testCaseId'])->first();
            $modifiedTestStepOrders = TestCaseTestStepOrder::with('testStep')
                                    ->where('TestCaseId','=',$request['testCaseId'])
                                    ->orderBy('test_case_test_step_orders.Order','ASC')
                                    ->get(
                                        [
                                            'TestStepId',
                                            'Order'
                                        ]
                                    )
                                    ->toArray();

            $testCase['TestStepOrder'] = $modifiedTestStepOrders;   
            
            return response()->
            json(['result' => $testCase], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create test step.']], 500
              );        
        }
    }
}
