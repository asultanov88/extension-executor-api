<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestCase;
use App\Models\TestCaseTestStepOrder;
use App\Models\DirectoryTestCase;
use App\Models\UserProject;
use App\Models\Directory;
use App\Models\TestCaseExecution;
use Exception;

class TestCaseController extends Controller
{
    private static $importedTestCases = array();

    /**
     * Search for a test case by title.
     */
    public function getTestCaseSearch(Request $request){
        $request->validate([
            'title'=>'required|min:2|max:500',
            'includeDeleted'=>'required|boolean',
        ]);

        // Only test cases that belong to the user projects are returned.
        $userProjects = UserProject::where('userProfileId','=',$request->user['userProfileId'])->orderBy('projectId', 'ASC')->get()->toArray();
        $userProjectIds = array_column($userProjects, 'projectId');

        try {
            $result = null;
            if(!$request['includeDeleted']){
                $result = TestCase::where('title','like', '%'.$request['title'].'%')
                    ->whereIn('projectId', $userProjectIds)
                    ->where('deleted','=','0')
                    ->limit(10)
                    ->orderBy('title','asc')
                    ->get();
            }else{
                $result = TestCase::where('title','like', '%'.$request['title'].'%')
                ->whereIn('projectId', $userProjectIds)
                ->limit(10)
                ->orderBy('title','asc')
                ->get();
            }

            return response()->
            json(['result' => $result], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to search for test case.']], 500
              );        
        }
    }

    public function deleteTestCase(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
        ]);

        $testCase = TestCase::where('testCaseId','=',$request['testCaseId'])->first();
        $userProject = UserProject::where('userProfileId','=',$request->user['userProfileId'])
                                  ->where('projectId','=',$testCase['projectId'])
                                  ->first();

        try {
            // Proceed only if user has access to the project of the test case.
            if(!is_null($userProject)){
                $testCase->update([
                    'deleted' => 1
                ]);
                return response()->json(['result' => ['message' => 'success']], 200);
            }else{
                return response()->json(['result' => ['message' => 'User has no access to this project.']]); 
            }
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete test case.']], 500
              );        
        }
    }

    public function patchTestCase(Request $request){
        $request->validate([
            'testCaseId'=>'required|integer|exists:test_cases,testCaseId',
            'title'=>'required|max:500',
            'projectId'=>'required|integer|exists:directories,directoryId',
            'directoryId'=>'required|integer|exists:directories,directoryId',
        ]);

        // Check if user has access to this project.
        $userProject = UserProject::where('userProfileId','=',$request->user['userProfileId'])
                                  ->where('projectId','=',$request['projectId'])
                                  ->first();
        if(is_null($userProject)){
            return response()->json(
                ['result' => ['message' => 'User has no access to this project.']]
                );  
        }

        // Check if project exists.
        $project = Directory::where('directoryId','=',$request['projectId'])->where('isProject','=',1)->first();
        if(is_null($project)){
            return response()->json(
                ['result' => ['message' => 'Project Id is invalid.']]
                );  
        }

        // Check is directoryId and projectId are related.
        $projectDirectory = Directory::where('directoryId','=',$request['directoryId'])->first();
        if((is_null($projectDirectory['projectId']) && $projectDirectory['directoryId'] !== $request['projectId'])
            || (!is_null($projectDirectory['projectId']) && $projectDirectory['projectId'] !== $request['projectId'])
        ){
            return response()->json(
                    ['result' => ['message' => 'Project Id and directory Id are not related.']]
                ); 
        }       

        try {
            $testCase = TestCase::where('testCaseId','=',$request['testCaseId'])->first();
            $testCase->update([
                'title' => $request['title'],
                'projectId' => $request['projectId'],
                'lastUpdatedBy' => $request->user['userProfileId'],
            ]);
            
            // Delete from the existing directory.
            DirectoryTestCase::where('testCaseId','=',$request['testCaseId'])->delete();
            // Insert into new directory.
            $directoryTestCase = new DirectoryTestCase();
            $directoryTestCase['directoryId'] = $request['directoryId'];
            $directoryTestCase['testCaseId'] = $request['testCaseId'];
            $directoryTestCase->save();

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
            'directoryId'=>'required|integer|exists:directories,directoryId',
            'projectId'=>'required|integer|exists:directories,directoryId',
        ]);
        
        // Check if project exists.
        $project = Directory::where('directoryId','=',$request['projectId'])->where('isProject','=',1)->first();
        if(is_null($project)){
            return response()->json(
                ['result' => ['message' => 'Project Id is invalid.']]
              );  
        }

        try {
            $testCase = new TestCase();
            $testCase['title'] = $request['title'];
            $testCase['projectId'] = $request['projectId'];
            $testCase['createdBy'] = $request->user['userProfileId'];            
            $testCase['lastUpdatedBy'] = $request->user['userProfileId'];            
            $testCase->save();
            $newTestCase = TestCase::where('testCaseId','=',$testCase->testCaseId)->first();

            // Insert to directory test cases.
            $directoryTestCase = new DirectoryTestCase();
            $directoryTestCase['testCaseId'] = $newTestCase['testCaseId'];
            $directoryTestCase['directoryId'] = $request['directoryId'];
            $directoryTestCase->save();
            
            return response()->
            json(['result' => $newTestCase], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create test case.']], 500
              );        
        }
    }

    /**
     * Get test case details by test case id.
     */
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
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get the test case']], 500
              );        
        }
    }

    /**
     * Gets test case with nested objects by test case id.
     * Used internally, not linked to API endpoint.
     */
    public static function getTestCaseDetailsById($testCaseId){
        $testCase = TestCaseController::getTestCaseDetails($testCaseId);
        // Imported test cases are stored in static variable to provide flat array.
        $testCase['importedTestCases'] = TestCaseController::$importedTestCases;
        $testCase['executionHistory'] = TestCaseExecution::where('testCaseId','=',$testCaseId)
                                        ->leftJoin('results','test_case_executions.resultId','=','results.resultId')
                                        ->leftJoin('statuses','test_case_executions.statusId','=','statuses.statusId')
                                        ->leftJoin('users','test_case_executions.executedBy','=','users.id')
                                        ->get([
                                            'test_case_executions.testCaseExecutionId',
                                            'test_case_executions.created_at',
                                            'test_case_executions.updated_at',
                                            'results.description AS result',
                                            'statuses.description AS status',
                                            'users.firstName',
                                            'users.lastName',
                                        ]);
        
        return $testCase;
    }

    private static function getTestCaseDetails($testCaseId){
        $testCase = TestCase::where('testCaseId','=',$testCaseId)->first();
        $modifiedTestStepOrders = TestCaseTestStepOrder::with('testStep')
                                ->leftJoin('imported_test_cases', function($join)
                                {
                                    $join->on('imported_test_cases.testCaseId','=','test_case_test_step_orders.testCaseId')
                                    ->on('imported_test_cases.importOrder','=','test_case_test_step_orders.order');
                                })
                                ->where('test_case_test_step_orders.testCaseId','=',$testCaseId)
                                ->orderBy('test_case_test_step_orders.order','ASC')
                                ->get();
        $testCase['testStepOrder'] = $modifiedTestStepOrders;

        // Get imported test cases.
        $importedTestCaseIds = [];
        foreach ($modifiedTestStepOrders as $order) {
            if($order['importedTestCaseId'] != null){
                array_push($importedTestCaseIds, $order['importedTestCaseId']);
            }
            unset($order['testCaseId']);
            unset($order['importOrder']);
        }

        foreach ($importedTestCaseIds as $importedTestCaseId) {
            array_push(TestCaseController::$importedTestCases, TestCaseController::getTestCaseDetails($importedTestCaseId));
        }
        TestCaseController::$importedTestCases = array_unique(TestCaseController::$importedTestCases);
        return $testCase;
    }
}
