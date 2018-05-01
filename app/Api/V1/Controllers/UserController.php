<?php

namespace App\Api\V1\Controllers;

use App\Address;
use App\Employee;
use App\Employer;
use App\Job;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Category;
use App\Subcategory;
use Illuminate\Http\Request;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class UserController extends Controller {

    public function getUser($credentials, JWTAuth $JWTAuth) {
        $validator = Validator::make($credentials, [
            'token' => 'required',
        ]);
        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        return $JWTAuth->toUser($credentials['token']);
    }

    public function getUserData(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if (!$user) {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }

        if ($user['type'] == 0) {
            $profile = Employee::where('user_id', '=', $user['id'])->get()->first();
        } else {
            $profile = Employer::where('user_id', '=', $user['id'])->get()->first();
            $addresses = Address::where('user_id', '=', $user['id'])->get();
            $user['addresses'] = $addresses;
        }

        $categories = [];
        foreach (Category::all() as $category) {
            $subCategory = Subcategory::where('category_id', '=', $category['id'])->get();
            $categoryArray = array_merge($category->toArray(), ['subCategory' => $subCategory]);
            array_push($categories, $categoryArray);
        }

        return response()->json([
            'user' => array_merge($profile ? $profile->toArray() : [], $user->toArray()),
            'categories' => $categories,
        ]);
    }

    public function updateProfile(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            if ($user['type'] == 0) {
                $profile = Employee::where('user_id', '=', $user['id'])->first();
            } else {
                $profile = Employer::where('user_id', '=', $user['id'])->first();
            }
            User::where('id', $user['id'])
                ->update($request->user);
            if ($profile) {
                if ($user['type'] == 0) {
                    Employee::where('id', $profile['id'])
                        ->update($request->profile);
                } else {
                    Employer::where('id', $profile['id'])
                        ->update($request->profile);
                }
            } else {
                $profile = $request->profile;
                $profile['user_id'] = $user['id'];
                if ($user['type'] == 0) {
                    Employee::insert($profile);
                } else {
                    Employer::insert($profile);
                }
            }
            return response()->json([
                'message' => "ok",
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function updateLocation(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            User::where('id', '=', $user['id'])
                ->update(['latitude' => $request->coord['latitude'], 'longitude' => $request->coord['longitude']]);
            return response()->json([
                'message' => "ok",
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function updateLevel(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            User::where('id', '=', $user['id'])
                ->update(['purchase_level' => $request->purchase_level, 'purchase_time' => date('Y-m-d H:i:s')]);
            return response()->json([
                'message' => $request->purchase_level,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    //for employee
    public function updateAboutMe(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            User::where('id', '=', $user['id'])
                ->update(['about' => $request->about]);
            return response()->json([
                'message' => "ok",
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    //for employer
    public function getEmployees(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $employees = User::join('employees', 'users.id', '=', 'employees.user_id')
                ->whereRaw('employees.categories = employees.categories ^ ((employees.categories & ' . $request->filter['categories'] . ') ^ ' . $request->filter['categories'] . ') and 
                employees.available_days = employees.available_days ^ ((employees.available_days & ' . $request->filter['available'] . ') ^ ' . $request->filter['available'] . ') and 
                employees.languages = employees.languages ^ ((employees.languages & ' . $request->filter['languages'] . ') ^ ' . $request->filter['languages'] . ') and 
                employees.gender = ' . $request->filter['gender'])
                ->get();
            return response()->json([
                'employees' => $employees,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function getFreelancerData(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $freelancer = User::where('id', '=', $request->freelancer_id)->get(['portfolio1', 'portfolio2', 'portfolio3',
                'portfolio4', 'portfolio5', 'portfolio6', 'about'])[0];
            $products = Product::where('user_id', '=', $request->freelancer_id)->get();
            return response()->json([
                'freelancer' => $freelancer,
                'products' => $products,
                'pending' => $this->isPending($user['id'], $request->freelancer_id),
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function isPending($userId, $freelancerId) {
        return Job::whereRaw('user_id='.$userId.' and freelancer_id='.$freelancerId.' and status=0 and 
            ABS(TIMESTAMPDIFF(MINUTE, request_time, now())) <= 30')
            ->exists();
    }

    public function getAllUsers(Request $request, JWTAuth $JWTAuth){
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if (!$user) {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
        $users = User::all();
        if($users)
        {
            return $users;
        } else
        {
            return response()->error('ALERT_NO_USER', 500);
        }
    }
}
