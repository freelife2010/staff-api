<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Storage;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Exception\ValidationHttpException;
use Validator;

class S3ImageController extends Controller
{
    /**
     * Manage Post Request
     *
     * @return void
     */
    public function uploadImage(Request $request, JWTAuth $JWTAuth) {
//        $this->validate($request, [
//            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//        ]);

        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $isAvatar = true;
            $id = $user['id'];
        } else {
            return response()->json(['message' => "ALERT_NO_USER"]);
        }

        if ($request->file('image')) {
            $imageUrl = $this->putImageToS3($request->file('image'), ($isAvatar ? 'avatars/' : 'portfolios/').$id.'/');
            User::where('id', '=', $id)
                ->update(['avatar_url' => $imageUrl]);
            return response()->json(['url' => $imageUrl]);
        } else {
            return response()->json(['message' => "ALERT_UPLOAD_IMAGE_FAILED"]);
        }
    }

    public function putImageToS3($image, $path) {
        $imageFileName = $path.time().'.'.$image->getClientOriginalExtension();
        $s3 = Storage::disk('s3');
        $s3->put($imageFileName, file_get_contents($image), 'public');
        return $s3->url($imageFileName);
    }

    public function getUser($credentials, JWTAuth $JWTAuth) {
        $validator = Validator::make($credentials, [
            'token' => 'required',
        ]);
        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        return $JWTAuth->toUser($credentials['token']);
    }
}