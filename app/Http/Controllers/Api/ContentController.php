<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\VideoStream;
use App\Models\Content;
use App\Models\ContentComment;
use App\Models\ContentLike;
use App\Models\ContentReport;
use App\Models\User;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\VideoFilters;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as Mpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as SupportFFMpeg;

class ContentController extends Controller
{
    public function generateThumbnail($path, $filename) {
        if (file_exists($path)) {
            if (!in_array('video_thumbs', Storage::disk('public')->directories())) {
                Storage::disk('public')->makeDirectory('video_thumbs');
            }

            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($path);
            $video->frame(
                \FFMpeg\Coordinate\TimeCode::fromSeconds(1)
            )->save(
                storage_path('app/public/video_thumbs/' . $filename . ".jpg")
            );

            return true;
        } else {
            return false;
        }
    }
    public function myContent(Request $request) {
        $user = User::where('token', $request->token)->first();
        $query = Content::where('user_id', $user->id);
        if ($request->with != null) {
            $query = $query->with($request->with);
        }
        $contents = $query->get();

        return response()->json([
            'contents' => $contents,
        ]);
    }
    public function storeMulti (Request $request) {
        $token = $request->token;
        $videos = $request->file('videos');
        $filenames = [];
        $toConcat = [];
        foreach ($videos as $v => $video) {
            $fileName = $video->getClientOriginalName();
            $video->storeAs('public/video_to_concat/' . $token, $fileName);

            array_push($filenames, $fileName);
            // if ($v > 0) {
                array_push($toConcat, 
                    'video_to_concat/' . $token . '/' . $fileName
                );
            // }
            
            // Rename to .mp4
            // $names = explode(".", $fileName);
            // $names[count($names) - 1] = "mp4";
            // $fileName = implode(".", $names);
            // $ffmpeg->save($format, storage_path('app/public/video_to_concat/' . $token . '/' . $fileName));
        }

        $newFilename = Str::random(32);

        // ->addFilter(['-vf', 'scale=-2:480'])
        SupportFFMpeg::fromDisk('public')->open($toConcat)
        ->export()
        ->inFormat(new \FFMpeg\Format\Video\X264())
        ->resize(1920, 1080)
        ->concatWithTranscoding()
        ->save(
            storage_path('app/public/video_concat/' .$newFilename. ".mp4")
        );
        
        // Log::info(Storage::disk('local')->url('merged_video.mp4'));
    }
    public function store(Request $request) {
        $user = User::where('token', $request->token)->first();
        $video = $request->file('video');
        $videoFileName = $user->id."_".time()."_".$video->getClientOriginalName();
        $video->storeAs('public/user_videos', $videoFileName);

        $this->generateThumbnail(
            storage_path('app/public/user_videos/' . $videoFileName), $videoFileName
        );

        // getting hashtags
        $tags = null;
        preg_match_all("/(#\w+)/", $request->caption, $hashtags);
        if (count($hashtags[0]) > 0) {
            $tags = [];
            foreach ($hashtags[0] as $ht) {
                array_push($tags, str_replace("#", "", $ht));
            }
            $tags = implode(",", $tags);
        }

        $saveData = Content::create([
            'user_id' => $user->id,
            'caption' => $request->caption,
            'filename' => $videoFileName,
            'thumbnail' => $videoFileName . ".jpg",
            'visibility' => true,
            'likes_count' => 0,
            'comments_count' => 0,
            'tags' => $tags,
            // 'can_be_commented' => $request->can_be_commented,
            // 'can_be_shared' => $request->can_be_shared,
            'can_be_commented' => true,
            'can_be_shared' => true,
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }
    public function delete(Request $request) {
        $data = Content::where('id', $request->id);
        $content = $data->first();

        $deleteData = $data->delete();
        $deleteFile = Storage::delete('public/user_videos/' . $content->filename);

        if ($request->report_id != "") {
            $rep = ContentReport::where('id', $request->report_id)->update([
                'resolved' => true,
            ]);
        }

        return response()->json([
            'status' => 200,
        ]);
    }
    public function like($contentID, Request $request) {
        $c = Content::where('id', $contentID);
        $content = $c->first();
        $likes = [];
        $action = null;
        
        if ($content != null) {
            $u = User::where('token', $request->token);
            $user = $u->first();
            $l = ContentLike::where([
                ['content_id', $contentID],
                ['user_id', $user->id]
            ]);
            $likes = $l->get('id');

            if ($likes->count() > 0) {
                $l->delete();
                $c->decrement('likes_count');
                $u->decrement('likes_count');
                $action = "dislike";
            } else {
                ContentLike::create([
                    'user_id' => $user->id,
                    'content_id' => $contentID,
                ]);
                $c->increment('likes_count');
                $u->increment('likes_count');
                $action = "like";
            }
        }

        return response()->json([
            'status' => 200,
            'action' => $action,
        ]);
    }
    public function comment($contentID) {
        $comments = ContentComment::where('content_id', $contentID)
        ->with('user')
        ->orderBy('created_at', 'DESC')->paginate(20);

        return response()->json([
            'comments' => $comments,
        ]);
    }
    public function stream($contentID) {
        $content = Content::where('id', $contentID)->first();
        $path = public_path('storage/user_videos/' . $content->filename);

        $stream = new VideoStream($path);
        return response()->stream(function () use ($stream) {
            $stream->start();
        });
    }

    public function reportContent(Request $request) {
        $user = User::where('token', $request->token)->first();

        $saveData = ContentReport::create([
            'report_by_user_id' => $user->id,
            'content_id' => $request->content_id,
            'topic' => $request->topic,
            'notes' => $request->notes,
            'resolved' => false,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function reportedContent(Request $request) {
        $filter = [];
        if ($request->show_all == false) {
            array_push($filter, ['resolved', false]);
        }
        $contents = ContentReport::where($filter)
        ->with(['content.user', 'user'])
        ->orderBy('created_at', 'DESC')->paginate(25);

        return response()->json([
            'contents' => $contents,
        ]);
    }
}
