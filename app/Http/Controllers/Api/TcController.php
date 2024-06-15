<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingCenter;
use App\Models\TrainingCenterCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TcController extends Controller
{
    public function list() {
        $items = TrainingCenter::with('courses')->get();

        return response()->json([
            'items' => $items,
        ]);
    }
    public function store(Request $request) {
        $icon = $request->file('icon');
        $iconFileName = $icon->getClientOriginalName();

        $saveData = TrainingCenter::create([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'email' => $request->email,
            'password' => bcrypt('112233'),
            'phone' => $request->phone,
            'website' => $request->website,
            'country' => $request->country,
            'is_approved' => false,
            'icon' => $iconFileName,
        ]);

        $icon->storeAs('public/tc_icons', $iconFileName);

        // foreach ($courses as $course) {
        //     $imageName = Str::random(17).".png";
        //     $imageString = str_replace("data:image/png;base64,", "", $course['cover_uri']);
        //     Storage::disk('public')->put('tc_course_images/' . $imageName, base64_decode($imageString));
            
        //     $saveCourse = TrainingCenterCourse::create([
        //         'training_center_id' => $saveData->id,
        //         'title' => $course['title'],
        //         'description' => $course['description'],
        //         'is_online' => $course['is_online'],
        //         'is_certified' => $course['is_certified'],
        //         'cover' => $imageName,
        //         'duration' => $course['duration'],
        //         'lessons_count' => $course['lessons']
        //     ]);
        // }

        return response()->json([
            'tc' => $saveData
        ]);
    }
    public function courseStore($tcID, Request $request) {
        $cover = $request->file('cover');
        $coverFileName = $cover->getClientOriginalName();

        $saveCourse = TrainingCenterCourse::create([
            'training_center_id' => $tcID,
            'title' => $request->title,
            'description' => $request->description,
            'is_online' => $request->is_online,
            'is_certified' => $request->is_certified,
            'duration' => $request->duration,
            'lessons_count' => $request->lessons,
            'cover' => $coverFileName,
        ]);

        $cover->storeAs('public/tc_course_images', $coverFileName);

        return response()->json(['ok']);
    }
}
