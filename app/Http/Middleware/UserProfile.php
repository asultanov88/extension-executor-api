<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Models\UserPermission;

class UserProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $userProfile = AuthController::userProfile();   
        $user = [
            'userProfileId' => $userProfile->original->id,   
        ];
        $userPermissions = UserPermission::where('userId','=',$user['userProfileId'])->get();
        $userPermissionIds = [];
        foreach ($userPermissions as $permission) {
            array_push($userPermissionIds, $permission['permissionId']);
        }
        $user['permissions'] = $userPermissionIds;  
        $request['user'] = $user;
        return $next($request);
    }
}
