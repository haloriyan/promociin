<?php

namespace App\Http\Controllers\Api;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function login(Request $request) {
        $message = "Kombinasi username dan password tidak tepat";
        $data = Admin::where('username', $request->username);
        $user = $data->first();

        $status = 401;

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                $token = Str::random(32);
                $data->update([
                    'token' => $token
                ]);
                $user = $data->first();
                $message = "Berhasil login.";
                $status = 200;
            }
        } else {
            $message = "Kami tidak dapat menemukan akun Anda";
        }

        return response()->json([
            'message' => $message,
            'user' => $user,
            'status' => $status,
        ]);
    }
    public function logout() {
        // 
    }

    public function user(Request $request) {
        $data = new User;
        if ($request->q != null) {
            Log::info($request->q);
            $q = $request->q;
            $data = $data->where('name', 'LIKE', '%'.$q.'%')->orWhere('username', 'LIKE', '%'.$q.'%');
        }
        $users = $data->paginate(25);

        return response()->json([
            'users' => $users,
        ]);
    }
}
