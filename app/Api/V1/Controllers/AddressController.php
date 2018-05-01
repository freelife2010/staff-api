<?php

namespace App\Api\V1\Controllers;

use App\Address;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dingo\Api\Exception\ValidationHttpException;

class AddressController extends Controller {

    public function getUser($credentials, JWTAuth $JWTAuth) {
        $validator = Validator::make($credentials, [
            'token' => 'required',
        ]);
        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        return $JWTAuth->toUser($credentials['token']);
    }

    public function addAddress(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $addresses = [];
            foreach($request->address as $item) {
                $params = $item;
                $params['user_id'] = $user['id'];
                $address = new Address($params);
                if (!$address->save()) {
                    throw new HttpException(500, "Failed to add new address.");
                } else {
                    array_push($addresses, $address);
                }
            }
            return response()->json([
                'message' => $addresses,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function removeAddress(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            Address::where('id', '=', $request->address_id)->delete();
            return response()->json([
                'message' => "ok",
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }
}
