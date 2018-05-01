<?php

namespace App\Api\V1\Controllers\Authentication;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Request;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class LoginController extends Controller
{
    public function getUser($credentials, JWTAuth $JWTAuth) {
        $validator = Validator::make($credentials, [
            'token' => 'required',
        ]);
        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        return $JWTAuth->toUser($credentials['token']);
    }

    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $JWTAuth->attempt($credentials);

            if (!$token) {
                throw new AccessDeniedHttpException('ALERT_INVALID_EMAIL_PASSWORD');
            }

        } catch (JWTException $e) {
            throw new HttpException(500, $e->getMessage());
        }

        $user = $JWTAuth->toUser($token);
        if ($user['type'] != $request->type) {
            throw new AccessDeniedHttpException('ALERT_USER_TYPE_MISMATCH');
        }

        return response()->json([
            'token' => $token,
            'type' => $user['type'],
        ]);
    }

    public function logout(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);

        if ($user->update(['token' => null])) {
//TODO:            Device::where('user_id', '=', $user['id'])->delete();

            return response()->json([
                'message' => "ALERT_SIGNED_OUT",
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_ALREADY_SIGNED_OUT",
            ]);
        }
    }
}
