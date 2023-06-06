<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestStepExecution;
use App\Models\Screenshot;
use App\Models\TestStepExecutionScreenshot;

class ScreenshotController extends Controller
{
    // Gets screenshot blob.
    public function getScreenshot(Request $request){
        $request->validate([
            'testStepExecutionId'=>'required|integer|exists:test_step_executions,testStepExecutionId',
            'screenshotId'=>'required|integer|exists:screenshots,screenshotId',
        ]);

        $testStepExecutionScreenshot = TestStepExecutionScreenshot::where('testStepExecutionId','=',$request['testStepExecutionId'])
                                     ->where('screenshotId','=',$request['screenshotId'])
                                     ->first();

        try {

            if(!is_null($testStepExecutionScreenshot)){
                $screenshot = Screenshot::where('screenshotId','=',$request['screenshotId'])->first();
                return response()->json(['result' => $screenshot], 200);
            }else{
                return response()->
                    json(['result' => ['message' => 'Invalid screenshot request.']], 500);
            }            

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get screenshot.']], 500
            );        
        }
    }

    // Deletes screenshot from test step.
    public function deleteScreenshot(Request $request){
        $request->validate([
            'testStepExecutionId'=>'required|integer|exists:test_step_executions,testStepExecutionId',
            'screenshotId'=>'required|integer|exists:screenshots,screenshotId',
        ]);

        try {

            // Check if screenshotId is valued and belongs to the test step execution.
            // Check if test step was executed by the user.
            $testStepExecutionScreenshot = TestStepExecutionScreenshot::where('test_step_execution_screenshots.testStepExecutionId','=',$request['testStepExecutionId'])
                ->join('test_step_executions','test_step_executions.testStepExecutionId','=','test_step_execution_screenshots.testStepExecutionId')
                ->join('test_case_executions','test_case_executions.testCaseExecutionId','=','test_step_executions.testCaseExecutionId')
                ->where('test_case_executions.executedBy','=',$request->user['userProfileId'])
                ->where('test_step_execution_screenshots.screenshotId','=',$request['screenshotId'])
                ->first();

            if(!is_null($testStepExecutionScreenshot)){

                // Delete the screenshot link to the step execution.
                TestStepExecutionScreenshot::where('testStepExecutionId','=',$request['testStepExecutionId'])
                ->where('screenshotId','=',$request['screenshotId'])
                ->delete();

                // Delete the screenshot.
                Screenshot::where('screenshotId','=',$request['screenshotId'])->delete();

                return response()->json(['result' => 'success'], 200);

            }else{
                return response()->
                    json(['result' => ['message' => 'Unauthorized to delete this screenshot.']], 200);
            }            

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete screenshot.']], 500
            );        
        }
    }

    // Attaches screenshot to test step execution.
    public function postScreenshot(Request $request){
        $request->validate([
            'testStepExecutionId'=>'required|integer|exists:test_step_executions,testStepExecutionId',
            'blob'=>'required'
        ]);

        try {

            $testStepExecution = TestStepExecution::where('testStepExecutionId','=',$request['testStepExecutionId'])
                                ->join('test_case_executions','test_case_executions.testCaseExecutionId','=','test_step_executions.testCaseExecutionId')
                                ->where('test_case_executions.executedBy','=',$request->user['userProfileId'])
                                ->first();

            if(!is_null($testStepExecution)){

                $blob = null;

                // Check if blob has a prefix.
                if(str_contains($request['blob'], 'base64')){
                    $data = explode(',', $request['blob']);
                    // removing double quotes form the beggining and end.
                    $blob = trim($data[1],'"');
                }else{
                    $blob = $request['blob'];
                }

                $screenshot = new Screenshot();
                $screenshot['blob'] = $blob;
                $screenshot->save();
                $screenshot->refresh();

                // Map screenshot to test step execution.
                $testStepExecutionScreenshot = new TestStepExecutionScreenshot();
                $testStepExecutionScreenshot['testStepExecutionId'] = $testStepExecution->testStepExecutionId;
                $testStepExecutionScreenshot['screenshotId'] = $screenshot->screenshotId;
                $testStepExecutionScreenshot['screenshotUuId'] = $screenshot->uuid;
                $testStepExecutionScreenshot->save();   
                
                $result = [
                    'screenshotId' => $screenshot['screenshotId'],
                    'uuid' => $screenshot['uuid'],
                ];
            }
            
            return response()->
            json(['result' => $result], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to upload screenshot.']], 500
            );        
        }
    }
}
