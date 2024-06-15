<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Stream;
use App\Models\StreamAccess;
use App\Models\StreamChat;
use App\Models\StreamViewer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class LivestreamController extends Controller
{
    public function list() {
        $streams = Stream::with(['user'])->orderBy('created_at', 'DESC')->take(15)->get();

        return response()->json([
            'streams' => $streams
        ]);
    }
    public function start(Request $request) {
        $user = User::where('token', $request->token)->first();

        $content = Content::create([
            'user_id' => $user->id,
            'caption' => 'contoh stream',
            'visibility' => true,
            'likes_count' => 0,
            'comments_count' => 0,
            'can_be_commented' => true,
            'can_be_shared' => true,
        ]);

        $stream = Stream::create([
            'user_id' => $user->id,
            'stream_key' => $request->stream_key,
            'visibility' => 'public'
        ]);

        Content::where('id', $content->id)->update([
            'stream_id' => $stream->id,
        ]);

        return response()->json([
            'stream' => $stream,
        ]);
    }
    public function getDatas($key, Request $request) {
        $stream = Stream::where('stream_key', $key)->first();
        $chats = StreamChat::where('stream_id', $stream->id)->with(['user'])->take(35)->orderBy('created_at', 'DESC')->get();
        $viewers = StreamViewer::where('stream_id', $stream->id)->get();
        // $viewers = [];

        if ($request->token) {
            $user = User::where('token', $request->token)->first(['id']);
            $c = StreamViewer::where([
                ['stream_id', $stream->id],
                ['user_id', $user->id]
            ]);
            $check = $c->get(['id']);

            if ($check->count() == 0) {
                $saveAsViewer = StreamViewer::create([
                    'stream_id' => $stream->id,
                    'user_id' => $user->id,
                ]);
            } else {
                $c->update([
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
            }
        } else {
            // as streamer
            $cont = Content::where('stream_id', $stream->id);
            $content = $cont->first();

            if ($content->thumbnail == null) {
                // 
            }
        }

        foreach ($viewers as $viewer) {
            $diff = Carbon::parse($viewer->updated_at)->diffInMinutes(
                Carbon::now()
            );
            if ($diff > 1) {
                StreamViewer::where('id', $viewer->id)->delete();
            }
        }

        return response()->json([
            'chats' => $chats,
            'viewers' => $viewers,
        ]);
    }

    public function generateCode(Request $request) {
        $saveData = StreamAccess::create([
            'user_id' => $request->user_id,
            'code' => $request->code,
            'expiry' => Carbon::now()->addMinutes(30)->format('Y-m-d H:i:s'),
            'has_used' => false,
        ]);

        return response()->json(['ok']);
    }
    public function deleteCode(Request $request) {
        $deleteCode = StreamAccess::where('id', $request->code_id)->delete();

        return response()->json(['ok']);
    }
    public function authCode(Request $request) {
        $user = User::where('token', $request->token)->first();
        $c = StreamAccess::where([
            ['code', $request->code],
            ['user_id', $user->id]
        ]);
        $code = $c->first();

        if ($code != "" && !$code->has_used) {
            $c->update([
                'has_used' => true,
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Kode akses berhasil digunakan"
            ]);
        } else {
            return response()->json([
                'status' => 402,
                'message' => "Kode akses salah. Mohon coba lainnya"
            ]);
        }
    }
    public function liveCode(Request $request) {
        $codes = StreamAccess::orderBy('created_at', 'DESC')->with(['user'])->paginate(25);

        return response()->json([
            'codes' => $codes,
        ]);
    }
    public function postChat($key, Request $request) {
        $stream = Stream::where('stream_key', $key)->first();
        $user = User::where('token', $request->token)->first();
        
        $saveData = StreamChat::create([
            'body' => $request->body,
            'stream_id' => $stream->id,
            'user_id' => $user->id,
            'type' => 'text'
        ]);
    }

    public function post(Request $request) {
        $chunk = $request->file('chunk');
        $chunk->storeAs('public/chunk_temp', 'tes.mp4');
        
        $theVideo = public_path('storage/user_videos/1_1704195745_443B83EC-701D-49B7-9289-D6DB4F5DD15B.mp4');
        $newVideo = public_path('storage/chumk_temp/tes.mp4');
        
        FFMpeg::open([$theVideo, $newVideo])
        ->export()
        ->concatWithoutTranscoding()
        ->save($theVideo);

        Storage::delete('public/chunk_temp/tes.mp4');
        Log::info('chunk saved');
    }
    public function postss(Request $request) {
        $theVideo = public_path('storage/user_videos/1_1704195745_443B83EC-701D-49B7-9289-D6DB4F5DD15B.mp4');
        $buffer = base64_decode($request->video_buffer);
        
        $tempFile = public_path('stream_tmp/streamtmp_tesstream.mp4');
        Storage::put($tempFile, $buffer);

        // $video = FFMpeg::open($theVideo);

        return response()->json(['status' => 200]);
    }
}
