<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSkill as Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function list(Request $request) {
        $user = User::where('token', $request->token)->first();
        $skills = Skill::where('user_id', $user->id)->orderBy('level', 'DESC')->get();

        return response()->json([
            'skills' => $skills,
        ]);
    }
    public function store(Request $request) {
        $user = User::where('token', $request->token)->first();

        $saveData = Skill::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'level' => $request->level,
        ]);

        return response()->json([
            'message' => "Berhasil menambahkan keahlian"
        ]);
    }
    public function update(Request $request) {
        $data = Skill::where('id', $request->id);
        $skill = $data->first();

        $updateData = $data->update([
            'name' => $request->name,
            'level' => $request->level,
        ]);

        return response()->json([
            'message' => "Berhasil mengubah data keahlian " . $skill->name,
        ]);
    }
    public function delete(Request $request) {
        $data = Skill::where('id', $request->id);
        $skill = $data->first();

        $deleteData = $data->delete();
        
        return response()->json([
            'message' => "Berhasil menghapus keahlian"
        ]);
    }
}
