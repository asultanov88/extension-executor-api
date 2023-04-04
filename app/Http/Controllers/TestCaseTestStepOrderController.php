<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\TestCaseTestStepOrder;
use App\Models\ImportedTestCase;
use App\Http\Controllers\TestCaseController;

class TestCaseTestStepOrderController extends Controller
{
    public function postStepOrderChange(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'testSteps' => 'required|array|min:2|max:2',
            'testSteps.*.existingOrder' => 'required|integer',
            'testSteps.*.newOrder' => 'required|integer',
            ]);

        try {
            $testStepOneExistingOrder = $request['testSteps'][0]['existingOrder'];
            $testStepTwoExistingOrder = $request['testSteps'][1]['existingOrder'];

            $testStepOneNewOrder = $request['testSteps'][0]['newOrder'];
            $testStepTwoNewOrder = $request['testSteps'][1]['newOrder'];

            // Delete all test case test step mapping based on the changing test steps.
            TestCaseTestStepOrder::where('testCaseId','=',$request['testCaseId'])
                ->where(function($query) use ($testStepOneExistingOrder, $testStepTwoExistingOrder)
                {     
                $query->where('test_case_test_step_orders.order','=',$testStepOneExistingOrder)
                        ->orWhere('test_case_test_step_orders.order','=',$testStepTwoExistingOrder); 
                })->delete();

            // Delete all imported test case mapping based on the changing test steps.
            ImportedTestCase::where('testCaseId','=',$request['testCaseId'])
                ->where(function($query) use ($testStepOneExistingOrder, $testStepTwoExistingOrder)
                {     
                $query->where('imported_test_cases.importOrder','=',$testStepOneExistingOrder)
                        ->orWhere('imported_test_cases.importOrder','=',$testStepTwoExistingOrder); 
                })->delete();

            // Saving the first step order.
            $testStepOrderOne = new TestCaseTestStepOrder();
            $testStepOrderOne['testCaseId'] = $request['testCaseId'];
            $testStepOrderOne['testStepId'] = $request['testSteps'][0]['testStepId'];
            $testStepOrderOne['order'] = $testStepOneNewOrder;
            $testStepOrderOne->save();
            if(!is_null($request['testSteps'][0]['importedTestCaseId'])){
                $importedTestCase = new ImportedTestCase();
                $importedTestCase['testCaseId'] = $request['testCaseId'];
                $importedTestCase['importedTestCaseId'] = $request['testSteps'][0]['importedTestCaseId'];
                $importedTestCase['importOrder'] = $testStepOneNewOrder;
                $importedTestCase->save();
            }

            // Saving the second step order.
            $testStepOrderTwo = new TestCaseTestStepOrder();
            $testStepOrderTwo['testCaseId'] = $request['testCaseId'];
            $testStepOrderTwo['testStepId'] = $request['testSteps'][1]['testStepId'];
            $testStepOrderTwo['order'] = $testStepTwoNewOrder;
            $testStepOrderTwo->save(); 
            if(!is_null($request['testSteps'][1]['importedTestCaseId'])){
                $importedTestCase = new ImportedTestCase();
                $importedTestCase['testCaseId'] = $request['testCaseId'];
                $importedTestCase['importedTestCaseId'] = $request['testSteps'][1]['importedTestCaseId'];
                $importedTestCase['importOrder'] = $testStepTwoNewOrder;
                $importedTestCase->save();
            }
            
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
