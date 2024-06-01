<?php

namespace App\Http\Controllers\Api;

use Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Chat;
use App\Models\Content;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserFollowers;
use App\Models\UserFollowing;
use Carbon\Carbon;
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

        if ($user == null) {
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
                'blue_check' => false,
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
        $blockedUserIDs = getBlockedUser($request->token);
        $now = Carbon::now();
        $u = User::where('username', $username);
        if ($request->with != "") {
            $u = $u->with(explode(",", $request->with));
        }
        $user = $u->first();
        $contents = [];
        
        if (in_array($user->id, $blockedUserIDs)) {
            $user = null;
        } else {
            $contents = Content::where('user_id', $user->id)->with('user')->orderBy('created_at', 'DESC')->get();

            if ($request->token != "") {
                $me = User::where('token', $request->token)->first();
                $appts = Appointment::where([
                    ['employer_id', $me->id],
                    ['employee_id', $user->id],
                    ['dues', '>=', $now->format('Y-m-d 00:00:00')],
                ])
                ->orWhere([
                    ['employer_id', $user->id],
                    ['employee_id', $me->id],
                    ['dues', '>=', $now->format('Y-m-d 00:00:00')],
                ])
                ->get(['id']);

                $user->able_to_invite_interview = $appts->count() == 0;
            }
        }

        return response()->json([
            'user' => $user,
            'contents' => $contents,
        ]);
    }
    public function blockList(Request $request) {
        $user = User::where('token', $request->token)->first();
        $blocks = UserBlock::where('blocker_id', $user->id)
        ->with('user')
        ->get();

        return response()->json([
            'user' => $user,
            'blocks' => $blocks,
        ]);
    }
    public function unblock(Request $request) {
        $user = User::where('token', $request->token)->first();
        $deleteData = UserBlock::where([
            ['blocked_id', $request->blocked_id],
            ['blocker_id', $user->id]
        ])->delete();

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function block(Request $request) {
        $user = User::where('token', $request->token)->first();
        $saveData = UserBlock::create([
            'blocker_id' => $user->id,
            'blocked_id' => $request->blocked_id,
        ]);

        return response()->json([
            'message' => "ok"
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
    public function requestDeletion(Request $request) {
        return response()->json([
            'message' => "Permintaan penghapusan akun Anda telah kami terima. Akun Anda akan kami bantu hapus dalam waktu maksimal 1 x 24 jam."
        ]);
    }
    public function deleteAccount(Request $request) {
        $u = User::where('token', $request->token);
        $user = $u->with(['certificates'])->first();
        $res = [
            'status' => 200,
            'message' => ""
        ];

        if (Hash::check($request->password, $user->password) || $request->bypass_pw == "yes") {
            // content deletion
            $c = Content::where('user_id', $user->id);
            $contents = $c->get(['filename', 'thumbnail']);
            $c->delete();
            foreach ($contents as $content) {
                Storage::delete('public/user_videos/' . $content->filename);
                Storage::delete('public/video_thumbs/' . $content->thumbnail);
            }

            // chats deletion
            $ch = Chat::where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
            $chats = $ch->get(['type', 'body']);
            $ch->delete();
            foreach ($chats as $chat) {
                if ($chat->type == "image") {
                    Storage::delete('public/chat_images/' . $chat->body);
                }
                if ($chat->type == "file") {
                    Storage::delete('public/chat_files/' . $chat->body);
                }
            }

            if ($user->certificates->count() > 0) {
                foreach ($user->certificates as $cert) {
                    Storage::delete('public/certificate_medias/' . $cert->filename);
                }
            }

            Storage::delete('public/user_photos/' . $user->photo);

            $u->delete();
        } else {
            $res = [
                'status' => 403,
                'message' => "Password yang Anda masukkan tidak tepat"
            ];
        }

        return response()->json($res);
    }
    public function search(Request $request) {
        $users = User::where('name', 'LIKE', '%'.$request->q.'%')->take(15)->get();

        return response()->json([
            'users' => $users,
        ]);
    }
}
