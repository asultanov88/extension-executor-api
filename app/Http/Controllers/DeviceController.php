<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    /**
     * Gets device list.
     */
    public function getDeviceList(Request $request){
        try {
           
            $deviceList = Device::where('deleted','=',0)->get();
            return response()->json(['result' => $deviceList], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get device list']], 500
              );        
        }
    }

    /**
     * Gets device by id.
     */
    public function getDevice(Request $request){
        $request->validate([
            'deviceId'=>'required|integer|exists:devices,deviceId',
        ]);

        try {
           
            // Only active devices are returned.
            $device = Device::where('deviceId','=',$request['deviceId'])->first();
            return response()->json(['result' => $device], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get device details']], 500
              );        
        }
    }

    /**
     * Deletes device.
     */
    public function deleteDevice(Request $request){
        $request->validate([
            'deviceId'=>'required|integer|exists:devices,deviceId',
        ]);

        try {
           
            $device = Device::where('deviceId','=',$request['deviceId'])->first();
            $device->update([
                'deleted' => 1,
                'updatedBy' => $request->user['userProfileId'],
            ]);
            return response()->json(['result' => ['message' => 'success']], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to delete device']], 500
              );        
        }
    }

    /**
     * Edits existing device.
     */
    public function patchDevice(Request $request){
        $request->validate([
            'deviceId'=>'required|integer|exists:devices,deviceId',
            'name'=>'required|min:2|max:100',
        ]);

        // Device name must be unique.
        if(!is_null(Device::where('name','like','%'.$request['name'].'%')
            ->where('deleted','=',0)
            ->where('deviceId','!=',$request['deviceId'])
            ->first()))
        {
            return response()->json(
                ['result' => ['message' => 'Device name must be unique']], 500
              );
        }
        
        try {

            // Insert the new record first.
            $newDevice = new Device();
            $newDevice['name'] = $request['name'];
            $newDevice['deleted'] = 0;
            $newDevice['createdBy'] = $request->user['userProfileId'];
            $newDevice['updatedBy'] = $request->user['userProfileId'];
            $newDevice->save();
            $newDevice->refresh();
           
            $updatedDevice = Device::where('deviceId','=',$request['deviceId'])->first();
            // Device must be soft deleted, then created a new one for patch.
            // Otherwise historical execution devices will be affected.
            $updatedDevice->update([
                'deleted' => 1,
                'updatedBy' => $request->user['userProfileId'],
                'replacedByDeviceId' => $newDevice['deviceId'],
            ]);

            return response()->json(['result' => $newDevice], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to update device']], 500
              );        
        }
    }

    /**
     * Posts a new device.
     */
    public function postDevice(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:100',
        ]);
        
        // Device name must be unique.
        if(!is_null(Device::where('name','like','%'.$request['name'].'%')->where('deleted','=',0)->first())){
            return response()->json(
                ['result' => ['message' => 'Device name must be unique']], 500
              );
        }

        try {
           
            $device = new Device();
            $device['name'] = $request['name'];
            $device['deleted'] = 0;
            $device['createdBy'] = $request->user['userProfileId'];
            $device['updatedBy'] = $request->user['userProfileId'];
            $device->save();
            $device->refresh();

            return response()->json(['result' => $device], 200);

        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create device']], 500
              );        
        }
    }
}
