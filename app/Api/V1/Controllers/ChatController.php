<?php

namespace App\Api\V1\Controllers;

use App\Application;
use App\Group;
use App\Job;
use App\JobSubcategory;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class ChatController extends Controller {

    public function getUser($credentials, JWTAuth $JWTAuth) {
        $validator = Validator::make($credentials, [
            'token' => 'required',
        ]);
        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        return $JWTAuth->toUser($credentials['token']);
    }

    //for employer
    public function getGroupByUser(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $group = Group::where('job_id', '=', $request->job_id)
                ->where('creator_id', '=', $user['id'])
                ->where('members', 'like', '%' . $request->user_id . ',%')
                ->get()->first();
            if (!$group) {
                $group = new Group();
                $group['job_id'] = $request->job_id;
                $group['creator_id'] = $user['id'];
                $group['members'] = $request->user_id . ',';
                $group['created_at'] = date('Y-m-d H:i:s');
                $group->save();
            }

            return response()->json([
                'message' => $group,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    //for both
    public function getGroups(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            if ($user['type'] == 0) {//employee
                $groups = Group::join('jobs','groups.job_id', '=', 'jobs.id')
                    ->where('members', 'like', '%' . $user['id'] . '%')
                    ->orderBy('groups.created_at')
                    ->get(['jobs.id as job_id', 'jobs.category_id', 'jobs.title', 'jobs.status', 'jobs.created_at as job_created_at', 'groups.*']);
            } else {//employer
                $groups = Group::join('jobs','groups.job_id', '=', 'jobs.id')
                    ->where('groups.creator_id', '=', $user['id'])
                    ->orderBy('groups.created_at')
                    ->get(['jobs.id as job_id', 'jobs.category_id', 'jobs.title', 'jobs.status', 'jobs.created_at as job_created_at', 'groups.*']);
            }

            $myGroups = [];
            foreach ($groups as $item) {
                $group = $item;
                $members = [];
                if ($user['type'] == 0) {
                    $member = User::join('employers', 'users.id', '=', 'employers.user_id')
                        ->where('users.id', '=', $item['creator_id'])
                        ->get(['users.id as user_id', 'users.avatar_url', 'employers.company_name as name'])
                        ->first();
                    array_push($members, $member);
                } else {
                    foreach (explode(',', $item['members']) as $subItem) {
                        if ($subItem) {
                            $member = User::join('employees', 'users.id', '=', 'employees.user_id')
                                ->where('users.id', '=', $subItem)
                                ->get(['users.id as user_id', 'users.avatar_url', 'employees.first_name as name'])
                                ->first();
                            array_push($members, $member);
                        }
                    }
                }
                $group['members'] = $members;
                array_push($myGroups, $group);
            }

            return response()->json([
                'message' => $groups,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }
}
