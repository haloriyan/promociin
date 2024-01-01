<?php

namespace App\Http\Controllers\Api;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\User;
use App\Models\UserFollowers;
use App\Models\UserFollowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function login(Request $request) {
        $res = [
            'status' => 200,
            'message' => "Berhasil login"
        ];
        $u = User::where('email', $request->email);
        $user = $u->first();

        if ($u == null) {
            $res = [
                'status' => 500,
                'message' => "Kami tidak dapat menemukan akun Anda"
            ];
        } else {
            if (Hash::check($request->password, $user->password)) {
                $token = Str::random(32);
                $u->update(['token' => $token]);
                $user = $u->first();
                $res['user'] = $user;
                $res['token'] = $token;

                $createOtp = OtpController::create($user, 'login');
            } else {
                $res = [
                    'status' => 500,
                    'message' => "Kombinasi email dan password tidak tepat"
                ];
            }
        }

        return response()->json($res);
    }
    public function register(Request $request) {
        $email = $request->email;
        $e = explode("@", $email);
        $token = Str::random(32);
        $res = [
            'status' => 200,
            'message' => "Berhasil membuat akun"
        ];

        $user = User::where('email', $email)->get('id');
        if ($user->count() > 0) {
            $res = [
                'status' => 500,
                'message' => "Email telah digunakan. Mohon gunakan alamat email yang lain"
            ];
        } else {
            $saveData = User::create([
                'name' => $request->name,
                'about' => "I just found this wonderful app",
                'email' => $email,
                'username' => $e[0],
                'password' => bcrypt($request->password),
                'photo' => null,
                'token' => $token,
                'followers_count' => 0,
                'following_count' => 0,
                'likes_count' => 0,
            ]);
    
            $createOtp = OtpController::create($saveData, 'register');

            $res['user'] = $saveData;
        }

        return response()->json($res);
    }
    public function auth(Request $request) {
        $query = User::where('token', $request->token);
        $user = $query->first();

        $res = ['status' => 200];
        if ($user == null) {
            $res['status'] = 401;
        } else {
            $res['user'] = $user;
        }

        return response()->json($res);
    }
    public function profile($username, Request $request) {
        $username = base64_decode($username);
        $u = User::where('username', $username);
        if ($request->with != "") {
            $u = $u->with(explode(",", $request->with));
        }
        $user = $u->first();
        $contents = Content::where('user_id', $user->id)->with('user')->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'user' => $user,
            'contents' => $contents,
        ]);
    }
    public function follow($username, Request $request) {
        $u = User::where('token', $request->token);
        $user = $u->first();
        $t = User::where('username', $username);
        $target = $t->first();

        $following = UserFollowing::where([
            ['user_id', $user->id],
            ['following_user_id', $target->id]
        ])->get(['id']);

        if ($following->count() == 0) {
            $action = "follow";
            $saveFollowing = UserFollowing::create([
                'user_id' => $user->id,
                'following_user_id' => $target->id,
            ]);
            $saveFollower = UserFollowers::create([
                'user_id' => $target->id,
                'follower_user_id' => $user->id,
            ]);
            $u->increment('following_count');
            $t->increment('followers_count');
        } else {
            $action = "unfollow";
            UserFollowing::where([
                ['user_id', $user->id],
                ['following_user_id', $target->id]
            ])->delete();
            UserFollowers::where([
                ['user_id', $target->id],
                ['follower_user_id', $user->id]
            ])->delete();
            $u->decrement('following_count');
            $t->decrement('followers_count');
        }

        return response()->json([
            'message' => "ok",
            'action' => $action
        ]);
    }
    public function updateBio(Request $request) {
        $u = User::where('token', $request->token);
        $u->update([
            'about' => $request->about,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function updatePhoto(Request $request) {
        $photo = $request->file('photo');
        $photoFileName = $photo->getClientOriginalName();

        $u = User::where('token', $request->token);
        $u->update([
            'photo' => $photoFileName,
        ]);

        $photo->storeAs('public/user_photos', $photoFileName);
        
        return response()->json([
            'message' => "ok"
        ]);
    }
    public function updateBasic(Request $request) {
        $u = User::where('token', $request->token);
        $u->update([
            'name' => $request->name,
            'about' => $request->about,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }

    public function forgetPassword(Request $request) {
        $email = $request->email;
        $res = [
            'status' => 404,
            'message' => "Kami tidak dapat menemukan akun dengan email Anda",
            'token' => null,
            'user' => null,
        ];

        $data = User::where('email', $email);
        $user = $data->first();

        if ($user != "") {
            $res['status'] = 200;
            $res['token'] = Str::random(32);
            $res['message'] = "Berhasil mengirim OTP";
            $data->update(['token' => $res['token']]);
            $res['user'] = $data->first();
            $createOtp = OtpController::create($user, 'reset_password');
        }

        return response()->json($res);
    }
    public function resetPassword(Request $request) {
        $data = User::where('id', $request->user_id);
        $data->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'status' => 200
        ]);
    }
}
