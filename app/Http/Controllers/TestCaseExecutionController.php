<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\TestCaseController;
use App\Models\TestCaseExecution;
use App\Models\TestStepExecution;

class TestCaseExecutionController extends Controller
{
    private static $importedTestCases = array();
    private static $testStepIdsForExecution = array();

    /**
     * Gets test case execution details.
     */
    public function getTestCaseExecution(Request $request){
        $request->validate([
            'testCaseExecutionId'=>'required|integer|exists:test_case_executions,testCaseExecutionId',
        ]);

        try {
            $testCaseExecutionDetails = TestCaseExecutionController::getTestExecutionDetails($request['testCaseExecutionId']);

            // Compare executionTestStepIds with the actual testStepIds, if they are different, add message to the result.
            $executionTestStepIds = array_map(function($testStep) { return $testStep['testStepId'];}, $testCaseExecutionDetails['executionSteps']);
            $testCase = TestCaseController::getTestCaseDetailsById($testCaseExecutionDetails['testCaseExecution']['testCaseId']);
            // Reset the static variable to get new ids.
            TestCaseExecutionController::$testStepIdsForExecution = [];
            TestCaseExecutionController::$importedTestCases = $testCase['importedTestCases'];
            TestCaseExecutionController::parseTestSteps($testCase['testStepOrder']);
            $testCaseTestStepIds = TestCaseExecutionController::$testStepIdsForExecution;

            if($executionTestStepIds !== $testCaseTestStepIds){
                $testCaseExecutionDetails['message'] = 'Test case has been changed since this execution. Please initiate a new execution to apply the latest test case changes.';
            }

            return response()->
            json(['result' => $testCaseExecutionDetails], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get test case execution details.']], 500
              );        
        }
    }

    /**
     * Initiates test case execution
     */
    public function postTestCaseExecution(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
        ]);

        try {
            // Check if test case has any test steps. Empty test cases cannot be executed.
            $testCase = TestCaseController::getTestCaseDetailsById($request['testCaseId']);
            if(count($testCase['testStepOrder']) < 1){
                return response()->json(
                    ['result' => ['message' => 'Unable to execute empty test case.']], 500
                  );  
            }

            // Insert TestCaseExecution.
            $testCaseExecution = new TestCaseExecution();
            $testCaseExecution['testCaseId'] = $request['testCaseId'];
            $testCaseExecution['statusId'] = 4; // not executed test case.
            $testCaseExecution['resultId'] = 4; // not executed test case.
            $testCaseExecution['executedBy'] = $request->user['userProfileId'];
            $testCaseExecution->save();
            $testCaseExecutionId = $testCaseExecution->testCaseExecutionId;

            // Insert TestStepExecution.
            TestCaseExecutionController::$importedTestCases = $testCase['importedTestCases'];
            TestCaseExecutionController::parseTestSteps($testCase['testStepOrder']);
            $sequence = 1; // Test step sequence as it shows up in test case.
            foreach(TestCaseExecutionController::$testStepIdsForExecution as $testStepId){
                $testStepExecution = new TestStepExecution();
                $testStepExecution['testCaseExecutionId'] = $testCaseExecutionId;
                $testStepExecution['testStepId'] = $testStepId;
                $testStepExecution['resultId'] = 4; // not executed step.
                $testStepExecution['sequence'] = $sequence;
                $testStepExecution->save();
                $sequence++; // Increment $sequence for each step.
            }

            $testCaseExecutionDetails = TestCaseExecutionController::getTestExecutionDetails($testCaseExecutionId);

            return response()->
            json(['result' => $testCaseExecutionDetails], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to execute test case.']], 500
              );        
        }
    }

    /**
     * Gets test case execution details.
     */
    public function getTestExecutionDetails($testCaseExecutionId){

        $testCaseExecution = TestCaseExecution::where('testCaseExecutionId','=',$testCaseExecutionId)
                                ->join('test_cases','test_cases.testCaseId','=','test_case_executions.testCaseId')
                                ->join('results','test_case_executions.resultId','=','results.resultId')
                                ->join('statuses','test_case_executions.statusId','=','statuses.statusId')
                                ->join('users','test_case_executions.executedBy','=','users.id')
                                ->first([
                                    'test_case_executions.testCaseExecutionId',
                                    'test_case_executions.testCaseId',
                                    'test_case_executions.created_at',
                                    'test_case_executions.updated_at',
                                    'statuses.description AS status',
                                    'results.description AS result',
                                    'users.lastName',
                                    'users.firstName',
                                    'test_cases.title AS testCaseTitle',
                                ]);

        $testStepsForExecution = TestStepExecution::where('testCaseExecutionId','=',$testCaseExecutionId)
                                    ->join('test_steps','test_steps.testStepId','=','test_step_executions.testStepId')
                                    ->join('results','test_step_executions.resultId','=','results.resultId')
                                    ->orderBy('test_step_executions.sequence', 'ASC')
                                    ->get([
                                    'test_step_executions.testStepExecutionId',
                                    'test_step_executions.sequence',
                                    'test_step_executions.actualResult',
                                    'results.description AS result',
                                    'test_steps.testStepId',
                                    'test_steps.description',
                                    'test_steps.expected',                                        
                                    ])->toArray();

        $result = [
            'testCaseExecution' =>  $testCaseExecution,
            'executionSteps' => $testStepsForExecution,
        ];

        return $result;
    }

    /**
     * Parses a given set of test steps to extract testStepIds.
     */
    private function parseTestSteps($testStepOrder){
        foreach ($testStepOrder as $testStep) {
            if(!is_null($testStep['testStepId'])){
                array_push(TestCaseExecutionController::$testStepIdsForExecution, $testStep['testStepId']);
            }else{
                // Start parsing imported test cases.
                $importedTestCaseId = $testStep['importedTestCaseId'];
                $importedTestCase = null;
                foreach(TestCaseExecutionController::$importedTestCases as $import){
                    if($import['testCaseId'] == $importedTestCaseId){
                        $importedTestCase = $import;
                        break;
                    }
                }
                if(!is_null($importedTestCase)){
                    TestCaseExecutionController::parseTestSteps($importedTestCase['testStepOrder']);
                }
            }
            
        }

    }
}
