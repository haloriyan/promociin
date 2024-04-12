<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function loginPage(Request $request) {
        if (Auth::guard('admin')->check()) {
            if ($request->r != "") {
                $r = base64_decode($request->r);
                return redirect($r);
            } else {
                return redirect()->route('admin.dashboard');
            }
        }
        $message = Session::get('message');
        
        return view('admin.login', [
            'message' => $message,
            'request' => $request,
        ]);
    }
    public function login(Request $request) {
        $loggingIn = Auth::guard('admin')->attempt([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if (!$loggingIn) {
            return redirect()->route('admin.loginPage')->withErrors(['Wrong username and password combination']);
        }

        if ($request->r != "") {
            $r = base64_decode($request->r);
            return redirect($r);
        } else {
            return redirect()->route('admin.dashboard');
        }
    }
    public function logout() {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.loginPage')->with([
            'message' => "Successfully logged out"
        ]);
    }

    public function dashboard() {
        $admin = Auth::guard('admin')->user();

        return view('admin.dashboard', [
            'admin' => $admin,
        ]);
    }
    public function ad() {
        $admin = Auth::guard('admin')->user();

        return view('admin.ad', [
            'admin' => $admin,
        ]);
    }
}
