<?php

namespace App\Api\V1\Controllers\Authentication;

use Config;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SignUpController extends Controller
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        if (User::where('email', '=', $request->get('email'))->exists()) {
            throw new HttpException(500, "ALERT_USER_EXIST");
        } else {
            $user = new User($request->all());
            if (!$user->save()) {
                throw new HttpException(500, "ALERT_REGISTER_FAILED");
            }
        }

        $token = Config::get('boilerplate.sign_up.release_token') ? $JWTAuth->fromUser($user) : null;

        return response()->json([
            'message' => "ALERT_REGISTERED",
            'token' => $token
        ]);
    }
}
