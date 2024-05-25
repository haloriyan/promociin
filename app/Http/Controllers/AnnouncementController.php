<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function fetch() {
        $announcements = Announcement::orderBy('created_at', 'DESC')->take(25)->get();
        
        return response()->json([
            'announcements' => $announcements,
        ]);
    }
    public function store(Request $request) {
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageFileName = $image->getClientOriginalName();
            $image->storeAs('public/announcement_images', $imageFileName);
        }

        $saveData = Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'image' => $imageFileName,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
}
