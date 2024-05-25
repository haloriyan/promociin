<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdClick;
use App\Models\AdView;
use App\Models\User;
use Carbon\Carbon;
use FFMpeg\Media\AdvancedMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function list() {
        $ads = Ad::all();

        return response()->json(['ads' => $ads]);
    }
    public function fetch(Request $request) {
        $adsRaw = Ad::all(['id']);
        $totalAds = $adsRaw->count() - 1;
        $ads = [];
        $maxAdNumber = 5;
        $now = Carbon::now()->format('Y-m-d');

        $user = User::where('token', $request->token)->first();

        for ($i = 0; $i < $maxAdNumber; $i++) {
            $adIndex = rand(0, $totalAds);
            $ad = Ad::where('id', $adsRaw[$adIndex]->id)->first();
            array_push($ads, $ad);
        }

        $hasHitted = [];
        foreach ($ads as $ad) {
            if (!in_array($ad->id, $hasHitted)) {
                array_push($hasHitted, $ad->id);
                $adViewQuery = AdView::where([
                    ['ad_id', $ad->id],
                    ['date', $now],
                ]);
                
                $toSave = [
                    'ad_id' => $ad->id,
                    'date' => $now,
                    'hit' => 1,
                ];
                if ($user != null) {
                    $adViewQuery = $adViewQuery->where('user_id', $user->id);
                    $toSave['user_id'] = $user->id;
                } else {
                    $adViewQuery = $adViewQuery->whereNull('user_id');
                    $toSave['user_id'] = null;
                }
    
                $adView = $adViewQuery->first();
                if ($adView == null) {
                    $saveData = AdView::create($toSave);
                } else {
                    $adViewQuery->increment('hit');
                }
            }
        }

        return response()->json([
            'ads' => $ads,
        ]);
    }
    public function detail($id) {
        $ad = Ad::where('id', $id)->first();
        $ad->views_hit = 0;
        $ad->clicks_hit = 0;
        
        $viewsRaw = AdView::where('ad_id', $ad->id)->get();
        foreach ($viewsRaw as $view) {
            $ad->views_hit += $view->hit;
        }
        
        return response()->json([
            'ad' => $ad,
        ]);
    }
    public function click(Request $request) {
        $now = Carbon::now();
        $user = User::where('token', $request->token)->first();

        $d = AdClick::where([
            ['ad_id', $request->ad_id],
            ['date', $now->format('Y-m-d')]
        ]);
        $data = $d->get('id');

        if ($data->count() == 0) {
            $saveData = AdClick::create([
                'ad_id' => $request->ad_id,
                'user_id' => $user->id,
                'date' => $now->format('Y-m-d'),
                'hit' => 1,
            ]);
        } else {
            $d->increment('hit');
        }

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function create(Request $request) {
        $iconFileName = null;
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();
            $icon->storeAs('public/ad_icons', $iconFileName);
        }

        $saveData = Ad::create([
            'title' => $request->title,
            'description' => $request->description,
            'url' => $request->url,
            'icon' => $iconFileName
        ]);

        return response()->json([
            'message' => "Berhasil menambahkan item iklan"
        ]);
    }
    public function delete($id) {
        $data = Ad::where('id', $id);
        $ad = $data->first();

        $deleteData = $data->delete();
        if ($ad->icon != null) {
            $deleteIcon = Storage::delete('public/ad_icons/' . $ad->icon);
        }

        return response()->json([
            'message'=>'OK'
        ]);
    }

    public function views($id) {
        $ad = Ad::where('id', $id)->first();
        
        return response()->json([
            'ad' => $ad,
        ]);
    }
}
