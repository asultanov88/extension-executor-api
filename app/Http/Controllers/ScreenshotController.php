<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                $fileName = $screenshot['uuid'].'.png';
                $screenshot['blob'] = 'data:image/png;base64,'.base64_encode(Storage::disk('screenshots')->get($fileName));

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
                $screenshot = Screenshot::where('screenshotId','=',$request['screenshotId'])->first();
                Storage::disk('screenshots')->delete($screenshot['uuid'].'.png');
                $screenshot->delete();

                return response()->json(['result' => ['message' => 'success']], 200);

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

                $screenshot = new Screenshot();
                $screenshot->save();
                $screenshot->refresh();

                // Save screenshot as file.
                $fileName = $screenshot['uuid'].'.png';
                $decodedBlob = ScreenshotController::decodeBlob($request['blob']);
                Storage::disk('screenshots')->put($fileName, $decodedBlob);

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

    /**
     * Decodes blob.
     */    
    private function decodeBlob($blob){
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == "actual base64 string"
        $data = explode(',', $blob);
        // removing double quotes form the beggining and end.
        $data_base64 = trim($data[1],'"');
        return base64_decode($data_base64);
    }
}
