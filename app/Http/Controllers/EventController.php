<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventTestCase;

class EventController extends Controller
{
    /**
     * Add test cases to event for execution.
     */
    public function postTestCasesToEvent(Request $request){
        $request->validate([
            'eventId'=>'required|integer|exists:events,eventId',
            'testCasesIds' => 'required|array',
            'testCasesIds.*' => 'required|integer|exists:test_cases,testCaseId',
        ]);

        try {

            foreach($request['testCasesIds'] as $testCaseId){
                $eventTestCase = new EventTestCase();
                $eventTestCase['eventId'] = $request['eventId'];
                $eventTestCase['testCaseId'] = $testCaseId;
                $eventTestCase->save();
            }

            $result = EventTestCase::where('eventId','=',$request['eventId'])->get();
   
            return response()->
            json(['result' => $result], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to add test case to event']], 500
            );        
        }
    }

    /**
     * Patch event status.
     */
    public function patchEventStatus(Request $request){
        $request->validate([
            'eventId'=>'required|integer|exists:events,eventId',
            'status'=>'required|in:complete,cancel,in-progress,not-executed',
        ]);

        try {
   
            $event = Event::where('eventId','=',$request['eventId'])->first();
            $statusId = null;
            switch ($request['status']) {
                case 'complete':
                    $statusId = 1;
                    break;
                case 'in-progress':
                    $statusId = 2;
                    break;
                case 'cancel':
                    $statusId = 3;
                    break;
                case 'not-executed':
                    $statusId = 4;
                    break;
            }

            $event->update([
                'statusId' => $statusId
            ]);
            $event->refresh();

            return response()->
            json(['result' => $event], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to patch event status']], 500
            );        
        }
    }

    /**
     * Post new event.
     */
    public function postEvent(Request $request){
        $request->validate([
            'title'=>'required|min:2|max:500',
            'description'=>'min:2|max:500',
        ]);

        try {
   
            $event = new Event();
            $event['title'] = $request['title'];
            $event['description'] = $request['description'];
            $event['createdBy'] = $request->user['userProfileId'];
            $event['statusId'] = 4; // Not executed.
            $event->save();
            $event->refresh();

            return response()->
            json(['result' => $event], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create event']], 500
            );        
        }
    }
}
