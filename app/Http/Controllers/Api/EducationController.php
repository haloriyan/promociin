<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\User;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function list(Request $request) {
        $user = User::where('token', $request->token)->first();
        $educations = Education::where('user_id', $user->id)->orderBy('end_date', 'DESC')->get();

        return response()->json([
            'educations' => $educations,
        ]);
    }
    public function store(Request $request) {
        $user = User::where('token', $request->token)->first();

        $saveData = Education::create([
            'user_id' => $user->id,
            'institute_name' => $request->name,
            'major' => $request->major,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }
    public function delete(Request $request) {
        $data = Education::where('id', $request->id);
        $deleteData = $data->delete();

        return response()->json([
            'status' => 200,
        ]);
    }
}
