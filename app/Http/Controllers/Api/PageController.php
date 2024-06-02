<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Announcement;
use App\Models\Content;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;

class PageController extends Controller
{
    public function home(Request $request) {
        // Get blocked users
        $token = 'azoTukEyV5j4KHRMrxRbJ964mL319VFm';
        $blockedUserIDs = getBlockedUser($request->token);

        $tag = $request->tag;
        if ($tag != "") {
            $c = Content::where('tags', 'LIKE', '%'.$tag.'%')
            ->orWhere('caption', 'LIKE', '%'.$tag.'%')
            ->whereNotIn('user_id', $blockedUserIDs)
            ->orderBy('created_at', 'DESC');
        } else {
            $c = Content::whereNotIn('user_id', $blockedUserIDs)->orderBy('created_at', 'DESC');
        }
        $contents = $c->with(['user','likes','dislikes','stream'])->get();

        foreach ($contents as $c => $content) {
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

            if ($content->thumbnail == null && $content->stream_id != null) {
                $streamPath = "/var/www/livestreams/" . $content->stream->stream_key . "/0.ts";
                $isExists = file_exists(
                    $streamPath
                );

                if ($isExists) {
                    // creating thumbnail
                    $ffmpeg = FFMpeg::create();
                    $video = $ffmpeg->open($streamPath);
                    $thumbnailName = $content->stream->stream_key.".jpg";
                    $video->frame(
                        \FFMpeg\Coordinate\TimeCode::fromSeconds(5)
                    )->save(
                        storage_path('app/public/video_thumbs/' . $thumbnailName)
                    );

                    Content::where('id', $content->id)->update([
                        'thumbnail' => $thumbnailName
                    ]);
                    $contents[$c]['thumbnail'] = $thumbnailName;
                }
            }
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
        $blockedUserIDs = getBlockedUser($request->token);

        $contents = Content::where([
            ['caption', 'LIKE', '%'.$request->q.'%']
        ])
        ->with(['user'])
        ->take(20)->get();

        if ($request->q != "") {
            $accounts = User::where('name', 'LIKE', '%'.$request->q.'%')->whereNotIn('id', $blockedUserIDs)
            ->orWhere('about', 'LIKE', '%'.$request->q.'%')
            ->take(5)->get();
        }

        return response()->json([
            'contents' => $contents,
            'accounts' => $accounts,
        ]);
    }
}
