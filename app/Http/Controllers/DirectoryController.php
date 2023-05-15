<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Directory;
use App\Models\UserProject;
use App\Models\TestCase;
use App\Models\DirectoryTestCase;

class DirectoryController extends Controller
{
    /**
     * Create new directory within another directory.
     */
    public function postDirectory(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:255',
            'parentDirectoryId'=>'required|integer|exists:directories,directoryId',
            'projectId'=>'required|integer|exists:directories,directoryId',
        ]);

        // Check if project exists.
        $project = Directory::where('directoryId','=',$request['projectId'])->where('isProject','=',1)->first();
        if(is_null($project)){
            return response()->json(
                ['result' => ['message' => 'Project Id is invalid.']]
              );  
        }

        // Check if duplicate directory exists withih the same parent.
        $duplicate = Directory::where('parentDirectoryId','=',$request['parentDirectoryId'])->where('name','=',$request['name'])->first();
        if(!is_null($duplicate)){
            return response()->json(
                ['result' => ['message' => 'Directory names must be unique within parent folder.']]
              );  
        }

        try {
            $directory = new Directory();
            $directory['name'] = $request['name'];
            $directory['isProject'] = 0;
            $directory['parentDirectoryId'] = $request['parentDirectoryId'];
            $directory['projectId'] = $request['projectId'];
            $directory->save();

            $refreshDirectories = DirectoryController::getProjectsDirectories($request);
            
            return response()->
            json(['result' => $refreshDirectories], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create directory']], 500
              );        
        }
    }

    /**
     * Assign project to user.
     */
    public function postUserProject(Request $request){
        $request->validate([
            'userProfileId'=>'required|integer|exists:users,id',
            'projectId'=>'required|integer|exists:directories,directoryId',
        ]);

        // Check if directory is a project.
        $project = Directory::where('directoryId','=',$request['projectId'])->where('isProject','=',1)->first();
        if(is_null($project)){
            return response()->json(
                ['result' => ['message' => 'Only projects can be assigned to users.']]
              );  
        }

        // Check if project is already assigned to this user.
        $assignedProject = UserProject::where('userProfileId','=',$request['userProfileId'])->where('projectId','=',$request['projectId'])->first();
        if(!is_null($assignedProject)){
            return response()->json(
                ['result' => ['message' => 'This project is already assigned to this user.']]
              );  
        }

        try {
            $userProject = new UserProject();
            $userProject['userProfileId'] = $request['userProfileId'];
            $userProject['projectId'] = $request['projectId'];         
            $userProject->save();
            
            return response()->
            json(['result' => $userProject], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to assign project to user.']], 500
              );        
        }
    }

    /**
     * Get the list of all projects.
     */
    public function getAllProjects(Request $request){
        try {
            $projects = Directory::where('isProject','=',1)->get();
            // Change directoryId to projectId.
            foreach($projects as $project){
                $project['projectdId'] = $project['directoryId'];
                unset($project['directoryId']);
            }            
            return response()->
            json(['result' => $projects], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to get the list of projects.']], 500
              );        
        }
    }

    /**
     * Create new project (Project is treated as directory with isProject=1).
     */
    public function postProject(Request $request){
        $request->validate([
            'name'=>'required|min:2|max:255',
        ]);

        // Projects are unique by name.
        $checkForExisting = Directory::where('isProject','=',1)->where('name','=',$request['name'])->first();
        if(!is_null($checkForExisting)){
            return response()->json(
                ['result' => ['message' => 'Project already exists with this name.']]
              );  
        }

        try {
            $project = new Directory();
            $project['name'] = $request['name'];
            $project['isProject'] = 1;            
            $project['parentDirectoryId'] = null;            
            $project['projectId'] = null;            
            $project->save();
            
            return response()->
            json(['result' => $project], 200);
        } catch (Exception $e) {
            return response()->json(
                env('APP_ENV') == 'local' ? $e : ['result' => ['message' => 'Unable to create project.']], 500
              );        
        }
    }

    /**
     * Gets user's projects and directories.
     */
    public function getProjectsDirectories(Request $request){
        $request->validate([
            'user.userProfileId'=>'required|integer|exists:users,id',
        ]);

        $userProjects = UserProject::where('userProfileId','=',$request->user['userProfileId'])->orderBy('projectId', 'ASC')->get();
        $userProjectResult = [];
        // Each project is a directory with isProject=1;
        foreach($userProjects as $project){
            array_push($userProjectResult, DirectoryController::getDirectory($project['projectId']));
        }        
        return $userProjectResult;
    }

    /** Gets directory content by directoryId */
    private static function getDirectory($directoryId){
        $directory = Directory::where('directoryId','=',$directoryId)->first();
        if(!is_null($directory)){
            $childDirectories = Directory::where('parentDirectoryId','=',$directory['directoryId'])->orderBy('name', 'ASC')->get();
            $childDirResult = [];
            foreach($childDirectories as $childDirectory){                
                array_push($childDirResult, DirectoryController::getDirectory($childDirectory['directoryId']));
            }
            $directory['childDirectories'] = $childDirResult;
        } 
        $directory['testCases'] = DirectoryController::getDirectoryTestCases($directoryId);     
        return $directory;
    }

    /** Gets directory test cases */
    private static function getDirectoryTestCases($directoryId){
        $directoryTestCases = DirectoryTestCase::where('directoryId','=',$directoryId)->orderBy('testCaseId', 'ASC')->get();
        $testCasesResult = [];
        foreach($directoryTestCases as $directoryTestCase){
            $testCase = TestCase::where('testCaseId','=',$directoryTestCase['testCaseId'])->first();
            if(!is_null($testCase)){
                $testCaseResult = [
                    'testCaseId' => $testCase['testCaseId'],
                    'title' => $testCase['title'],
                ];
                array_push($testCasesResult, $testCaseResult);
            }
        }
        return $testCasesResult;
    }
}
