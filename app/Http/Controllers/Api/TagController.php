<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TagController extends Controller
{
    public function list() {
        $tags = Tag::orderBy('priority', 'DESC')->orderBy('updated_at', 'DESC')->get();

        return response()->json([
            'tags' => $tags,
        ]);
    }
    public function create(Request $request) {
        $iconFileName = null;
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();
            $icon->storeAs('public/tag_icons', $iconFileName);
        }

        $saveData = Tag::create([
            'icon' => $iconFileName,
            'name' => $request->name,
            'priority' => 0,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan tag"
        ]);
    }
    public function update(Request $request) {
        $data = Tag::where('id', $request->id);
        $tag = $data->first();

        $toUpdate = ['name' => $request->name];
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();
            if ($tag->icon != null) {
                $deleteOldIcon = Storage::delete('public/tag_icons/' . $tag->icon);
            }
            $icon->storeAs('public/tag_icons', $iconFileName);
            $toUpdate['icon'] = $iconFileName;
        }

        $updateData = $data->update($toUpdate);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengubah tag"
        ]);
    }
    public function delete(Request $request) {
        $data = Tag::where('id', $request->id);
        $tag = $data->first();

        $deleteData = $data->delete();
        if ($tag->icon != null) {
            $deleteIcon = Storage::delete('public/tag_icons/' . $tag->icon);
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus tag"
        ]);
    }
    public function priority(Request $request) {
        $data = Tag::where('id', $request->id);
        $tag = $data->first();

        if ($request->action == "increment") {
            $data->increment('priority');
        } else {
            $data->decrement('priority');
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengubah tag"
        ]);
    }
}
