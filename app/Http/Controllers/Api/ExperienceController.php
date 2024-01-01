<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserExperience as Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function store(Request $request) {
        $user = User::where('token', $request->token)->first();

        $saveData = Experience::create([
            'user_id' => $user->id,
            'position' => $request->position,
            'company' => $request->company,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'priority' => 0,
        ]);

        return response()->json([
            'message' => "Berhasil menambahkan pengalaman"
        ]);
    }
    public function update(Request $request) {
        $data = Experience::where('id', $request->id);
        $exp = $data->first();

        $updateData = $data->update([
            'position' => $request->position,
            'company' => $request->company,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'message' => "Berhasil mengubah data pengalaman"
        ]);
    }
    public function delete(Request $request) {
        $data = Experience::where('id', $request->id);
        $exp = $data->first();

        $deleteData = $data->delete();
        
        return response()->json([
            'message' => "Berhasil menghapus data pengalaman"
        ]);
    }

    public function mine(Request $request) {
        $user = User::where('token', $request->token)->first();
        $experiences = Experience::where('user_id', $user->id)->orderBy('priority', 'DESC')->orderBy('updated_at', 'DESC')->get();

        return response()->json([
            'experiences' => $experiences,
        ]);
    }
}
