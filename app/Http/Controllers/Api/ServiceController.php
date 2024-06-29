<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function fetch(Request $request) {
        $filter = [];
        if ($request->location != "") {
            array_push($filter, ['location', 'LIKE', '%'.$request->location.'%']);
        }
        if ($request->q != "") {
            array_push($filter, ['title', 'LIKE', '%'.$request->q.'%']);
        }

        $datas = Service::where($filter)->with(['user', 'packages'])->paginate(25);

        return response()->json([
            'datas' => $datas,
        ]);
    }
    public function store(Request $request) {
        $user = User::where('token', $request->token)->first();
        $pakets = json_decode($request->pakets, false);

        $cover = $request->file('cover');
        $coverFileName = $cover->getClientOriginalName();
        $saveData = Service::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'cover' => $coverFileName,
            'price' => 0,
            'location' => $request->location,
            'country' => $request->country,
        ]);

        $cover->storeAs('public/service_images', $coverFileName);

        $lowestPrice = 0;
        foreach ($pakets as $paket) {
            $savePaket = ServicePackage::create([
                'service_id' => $saveData->id,
                'name' => $paket->name,
                'description' => $paket->description,
                'price' => $paket->price,
                'benefits' => json_encode($paket->benefits),
            ]);

            if ($paket->price < $lowestPrice || $lowestPrice == 0) {
                $lowestPrice = $paket->price;
            }
        }

        $updatePrice = Service::where('id', $saveData->id)->update([
            'price' => $lowestPrice,
        ]);

        return response()->json(['ok']);
    }
    public function delete(Request $request) {
        $data = Service::where('id', $request->id);
        $service = $data->first();

        $deleteData = $data->delete();
        $deleteImage = Storage::delete('public/service_images/' . $service->cover);

        return response()->json(['ok']);
    }
}
