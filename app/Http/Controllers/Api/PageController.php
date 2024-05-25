<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Announcement;
use App\Models\Content;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function home(Request $request) {
        $tag = $request->tag;
        if ($tag != "") {
            $c = Content::where('tags', 'LIKE', '%'.$tag.'%')
            ->orWhere('caption', 'LIKE', '%'.$tag.'%')
            ->orderBy('created_at', 'DESC');
        } else {
            $c = Content::orderBy('created_at', 'DESC');
        }
        $contents = $c->with(['user','likes','dislikes'])->get();

        foreach ($contents as $c => $content) {
            // $contents[$c]['likers_id'] = [];
            $likers = [];
            $dislikers = [];
            foreach ($content->likes as $like) {
                array_push($likers, $like->user_id);
            }
            foreach ($content->dislikes as $like) {
                array_push($dislikers, $like->user_id);
            }
            $contents[$c]['likers_id'] = $likers;
            $contents[$c]['dislikers_id'] = $dislikers;
            $contents[$c]['i_have_reported'] = false;
        }

        $tags = Tag::orderBy('priority', 'DESC')->orderBy('updated_at', 'DESC')->get();
        $announcements = Announcement::orderBy('created_at', 'DESC')->take(25)->get();

        return response()->json([
            'contents' => $contents,
            'tags' => $tags,
            'announcements' => $announcements,
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
