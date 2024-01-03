<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCertificate as Certificate;
use App\Models\UserSkill as Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function list(Request $request) {
        $user = User::where('token', $request->token)->first();
        $certificates = Certificate::where('user_id', $user->id)->with(['skill'])->get();
        $skills = Skill::where('user_id', $user->id)->orderBy('level', 'DESC')->get();

        return response()->json([
            'certificates' => $certificates,
            'skills' => $skills,
        ]);
    }
    public function store(Request $request) {
        $user = User::where('token', $request->token)->first();

        $toSave = [
            'user_id' => $user->id,
            'skill_id' => $request->skill_id,
            'title' => $request->title,
            'url' => $request->url,
            'publisher' => $request->publisher,
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
        ];

        if ($request->hasFile('media')) {
            $media = $request->file('media');
            $mediaFileName = $media->getClientOriginalName();
            $toSave['filename'] = $mediaFileName;
            $media->storeAs('public/certificate_medias', $mediaFileName);
        }

        $saveData = Certificate::create($toSave);

        return response()->json([
            'status' => 200,
        ]);
    }
    public function delete(Request $request) {
        $data = Certificate::where('id', $request->id);
        $cert = $data->first();

        $deleteData = $data->delete();
        if ($cert->filename != null) {
            $deleteMedia = Storage::delete('public/certificate_medias/' . $cert->filename);
        }

        return response()->json([
            'status' => 200,
        ]);
    }
}
