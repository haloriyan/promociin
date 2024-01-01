<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function home(Request $request) {
        $contents = Content::orderBy('created_at', 'DESC')
        ->with(['user'])->get();

        return response()->json([
            'contents' => $contents
        ]);
    }
    public function explore(Request $request) {
        $accounts = [];
        $contents = [];

        $contents = Content::where([
            ['caption', 'LIKE', '%'.$request->q.'%']
        ])
        ->with(['user'])
        ->take(20)->get();

        if ($request->q != "") {
            $accounts = User::where('name', 'LIKE', '%'.$request->q.'%')
            ->orWhere('about', 'LIKE', '%'.$request->q.'%')
            ->take(5)->get();
        }

        return response()->json([
            'contents' => $contents,
            'accounts' => $accounts,
        ]);
    }
}
