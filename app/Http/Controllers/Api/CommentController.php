<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\ContentComment;
use App\Models\ContentCommentLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function get($contentID, Request $request) {
        $comments = ContentComment::where('content_id', $contentID)->orderBy('created_at', 'DESC')->with(['user'])->get();
        return response()->json([
            'comments' => $comments,
        ]);
    }
    public function store($contentID, Request $request) {
        $user = User::where('token', $request->token)->first();
        $c = Content::where('id', $contentID);
        
        $saveData = ContentComment::create([
            'user_id' => $user->id,
            'content_id' => $contentID,
            'body' => $request->body,
            'likes_count' => 0,
        ]);

        $c->increment('comments_count');

        return response()->json([
            'status' => 200,
        ]);
    }
    public function delete($contentID, Request $request) {
        $user = User::where('token', $request->token)->first();
        $data = ContentComment::where('id', $request->id);
        $comment = $data->first();
        $status = 200;

        if ($comment->user_id == $user->id) {
            $deleteData = $data->delete();
        } else {
            $status = 403;
        }

        return response()->json([
            'status' => $status,
        ]);
    }
    public function like($contentID, Request $request) {
        $user = User::where('token', $request->token)->first();
        $comm = ContentComment::where('id', $request->id);

        $l = ContentCommentLike::where([
            ['comment_id', $request->id],
            ['user_id', $user->id]
        ]);
        $likes = $l->get(['id']);

        if ($likes->count() == 0) {
            $saveLike = new ContentCommentLike();
            $saveLike->content_id = $contentID;
            $saveLike->comment_id = $request->id;
            $saveLike->user_id = $user->id;
            $saveLike->save();
            
            $comm->increment('likes_count');
        } else {
            $l->delete();
            $comm->decrement('likes_count');
        }

        return response()->json([
            'status' => 200,
        ]);
    }
}
