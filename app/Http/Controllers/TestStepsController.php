<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestStep;
use App\Models\TestCaseTestStepOrder;
use App\Models\TestCase;
use App\Models\ImportedTestCase;
use App\Http\Controllers\TestCaseController;
use Exception;

class TestStepsController extends Controller
{
    public function deleteTestStep(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'testStepId'=>'required|integer|exists:test_steps,testStepId',
            ]);

        try {
            $deleteStepOrder = TestCaseTestStepOrder::where('testCaseId','=',$request['testCaseId'])
            ->where('testStepId','=',$request['testStepId'])
            ->first();

            if($deleteStepOrder){
                $deletedTestStepOrder = $deleteStepOrder['order'];
                $deleteStepOrder->delete();
                // Rearrage test step orders after delete.
                $newStepOrder = TestCaseTestStepOrder::where('testCaseId','=',$request['testCaseId'])
                ->orderBy('test_case_test_step_orders.order','ASC')
                ->get();

                if(count($newStepOrder) > 0){
                    for ($i = 0; $i < count($newStepOrder); $i++) {
                        $newStepOrder[$i]->update(
                            [
                                'order' => $i+1
                            ]
                        );
                    }
                }          
            }
            
            // Pull the test case object with all children for return.
            $testCase = TestCaseController::getTestCaseDetailsById($request['testCaseId']);

            return response()->
            json(['result' => $testCase], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete test step.']], 500
            );        
        }
    }

    public function patchTestStep(Request $request){
        $request->validate([
            'description'=>'required',
            'expected'=>'required',
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'testStepId'=>'required|integer|exists:test_steps,testStepId',
            ]);

        try {
            // Create a new test step, then reassign it to the test case step order.
            // We do not update the existing test steps.
            $newTestStep = new TestStep();
            $newTestStep['testCaseId'] = $request['testCaseId'];
            $newTestStep['description'] = $request['description'];
            $newTestStep['expected'] = $request['expected'];
            $newTestStep->save();

            $updateStepOrder = TestCaseTestStepOrder::where('testCaseId','=',$request['testCaseId'])
                ->where('testStepId','=',$request['testStepId'])
                ->first();

            if($updateStepOrder){
                $updateStepOrder->update([
                    'testStepId' => $newTestStep->testStepId
                ]);
            }

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

    public function postImportedTestCase(Request $request){
        $request->validate([    
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'importedTestCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'order'=>'required|integer|min:1'
            ]);

        try {
            // Test cases cannot import each other directly or indirectly, this causes infinite loop.
            $importCheck = TestCaseController::getTestCaseDetailsById($request['importedTestCaseId']);
            $check = true;
            if($request['testCaseId'] == $request['importedTestCaseId']){
                $check = false;
            }

            if($check){
                foreach($importCheck['importedTestCases'] as $import){
                    if($request['testCaseId'] == $import['testCaseId']){
                        $check = false;
                        break;
                    }
                }
            }

            if(!$check){
                return response()->json(['result' => ['message' => 'Test case cannot be imported.']], 500);
            }

            // Check if test case already contains $request['order'].
            $this->incrementTestStepOrder($request['testCaseId'], $request['order']);

            $testCaseTestStepOrder = new TestCaseTestStepOrder();
            $testCaseTestStepOrder['testCaseId'] = $request['testCaseId'];
            $testCaseTestStepOrder['testStepId'] = null;
            $testCaseTestStepOrder['order'] = $request['order'];
            $testCaseTestStepOrder->save();

            $importedTestCase = new ImportedTestCase();
            $importedTestCase['testCaseId'] = $request['testCaseId'];
            $importedTestCase['importedTestCaseId'] = $request['importedTestCaseId'];
            $importedTestCase['importOrder'] = $request['order'];
            $importedTestCase->save();
               
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

    public function postTestStep(Request $request){
        $request->validate([
            'description'=>'required',
            'expected'=>'required',
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'order'=>'required|integer|min:1',
            ]);

        try {   
            $this->incrementTestStepOrder($request['testCaseId'], $request['order']);
                  
            // Save the new test step. 
            $testStep = new TestStep();
            $testStep['description'] = $request['description'];
            $testStep['expected'] = $request['expected'];
            $testStep['testCaseId'] = $request['testCaseId'];
            $testStep->save();
            $newTestStepId = $testStep->testStepId;

            // Insert the new step order number for the new test step.
            $newTestCaseTestStepOrder = new TestCaseTestStepOrder();
            $newTestCaseTestStepOrder['testCaseId'] = $request['testCaseId'];
            $newTestCaseTestStepOrder['testStepId'] = $newTestStepId;
            $newTestCaseTestStepOrder['order'] = $request['order'];
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

    // Increments test step order to make a room for the new insert.
    private function incrementTestStepOrder($testCaseId, $order){
        // Select and store existing imported test cases in memory.
        $importedTestCases = ImportedTestCase::where('testCaseId','=',$testCaseId)
        ->where('importOrder','>=',$order)
        ->get();

        // Delete existing imported test cases to remove FK references.
        ImportedTestCase::where('testCaseId','=',$testCaseId)
        ->where('importOrder','>=',$order)
        ->delete();

        // Select all existing test step orders (including imported test case orders).
        $existingTestStepOrders = TestCaseTestStepOrder::where('testCaseId','=',$testCaseId)
                    ->where('order','>=',$order)
                    ->orderBy('test_case_test_step_orders.order','DESC')
                    ->get();

        // increment each order to make a room for the new insert.
        foreach ($existingTestStepOrders as $stepOrder) {
            $stepOrder->update([
                'order' => $stepOrder['order'] + 1
            ]);
        }

        // reinsert imported test cases by incrementing importOrder.
        foreach ($importedTestCases as $import){ 
            $importedTestCase = new ImportedTestCase();
            $importedTestCase['testCaseId'] = $testCaseId;
            $importedTestCase['importedTestCaseId'] = $import['importedTestCaseId'];
            $importedTestCase['importOrder'] = $import['importOrder'] + 1;
            $importedTestCase->save();
        }
    }
}
