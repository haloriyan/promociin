<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class LivestreamController extends Controller
{
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
