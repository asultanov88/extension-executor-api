<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestStepExecution;
use App\Models\Screenshot;
use App\Models\TestStepExecutionScreenshot;

class ScreenshotController extends Controller
{
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
