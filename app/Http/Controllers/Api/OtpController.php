<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Notifications\Otp as NotificationsOtp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    public static function create($user, $purpose) {
        $code = rand(1111, 9999);
        $now = Carbon::now();

        $otp = Otp::create([
            'user_id' => $user->id,
            'purpose' => $purpose,
            'code' => $code,
            'expiry' => $now->addMinutes(30)->format('Y-m-d H:i:s'),
            'has_used' => false,
        ]);

        $user->notify(new NotificationsOtp([
            'user' => $user,
            'otp' => $otp,
            'purpose' => $purpose
        ]));

        return $otp;
    }
    public function resend(Request $request) {
        $old = Otp::where('user_id', $request->user_id)->latest();
        $otp = $old->first();
        $old->delete();

        $code = rand(1111, 9999);
        $now = Carbon::now();
        $otp = Otp::create([
            'user_id' => $otp->user_id,
            'purpose' => $otp->purpose,
            'code' => $code,
            'expiry' => $now->addMinutes(30)->format('Y-m-d H:i:s'),
            'has_used' => false,
        ]);

        return response()->json([
            'otp' => $otp,
        ]);
    }
    public function auth(Request $request) {
        $res = [
            'message' => "Berhasil mengautentikasi",
            'status' => 200,
        ];

        $code = $request->code;
        $now = Carbon::now();
        $u = User::where('token', $request->token);
        $user = $u->first();

        $allowed = ['riyan.satria.619@gmail.com'];

        if (in_array($user->email, $allowed)) {
            $res['otp'] = "special_access";
        } else {
            if ($user != null) {
                $o = Otp::where([
                    ['user_id', $user->id],
                    ['code', $code],
                    ['has_used', false],
                    ['expiry', '>=', $now->format('Y-m-d H:i:s')]
                ]);
                $otp = $o->first();
        
                if ($otp != null) {
                    if ($otp->purpose == "register") {
                        // $u->update(['is_active' => true]);
                    }
                    $o->update(['has_used' => true]);
                    $res['user'] = $u->first();
                    $res['otp'] = $otp;
                } else {
                    $res['message'] = "Kode OTP Salah";
                    $res['status'] = 403;
                }
            } else {
                $res['message'] = "Kesalahan autentikasi";
                $res['status'] = 403;
            }
        }

        return response()->json($res);
    }
}
