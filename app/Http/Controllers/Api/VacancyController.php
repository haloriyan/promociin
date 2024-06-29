<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VacancyController extends Controller
{
    public function fetch(Request $request) {
        $filter = [];
        if ($request->industry != "") {
            array_push($filter, ['industry', 'LIKE', '%'.$request->industry.'%']);
        }
        if ($request->location != "") {
            array_push($filter, ['location', 'LIKE', '%'.$request->location.'%']);
        }
        if ($request->type != "") {
            array_push($filter, ['type', 'LIKE', '%'.$request->type.'%']);
        }
        if ($request->q != "") {
            array_push($filter, ['title', 'LIKE', '%'.$request->q.'%']);
        }

        $datas = Vacancy::where($filter)->with(['user'])->paginate(25);

        return response()->json([
            'datas' => $datas,
        ]);
    }
    public function store(Request $request) {
        $user = User::where('token', $request->token)->first();

        $saveData = Vacancy::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'salary' => $request->salary,
            'industry' => $request->industry,
            'type' => $request->type,
            'location' => $request->location,
            'expiry_date' => $request->expiry_date,
        ]);

        return response()->json(['ok']);
    }
    public function detail($id, Request $request) {
        $user = User::where('token', $request->token)->first();
        $vacancy = Vacancy::where('id', $id)->first();
        $app = VacancyApplication::where([
            ['vacancy_id', $id],
            ['user_id', $user->id]
        ])->get(['id']);
        $currentDate = Carbon::now();
        $expDate = Carbon::parse($vacancy->expiry_date);

        $hasApplied = $app->count() > 0;
        $hasExpired = ($currentDate->lte($expDate) || $currentDate->isSameDay($expDate)) ? false : true;
        $applicants = VacancyApplication::where('vacancy_id', $id)->with(['user'])->get();

        return response()->json([
            'has_applied' => $hasApplied,
            'has_expired' => $hasExpired,
            'applicants' => $applicants,
        ]);
    }
    public function delete($id, Request $request) {
        $data = Vacancy::where('id', $id);
        $deleteData = $data->delete();

        return response()->json(['ok']);
    }
    public function update($id, Request $request) {
        $data = Vacancy::where('id', $id);
        
        $updateData = $data->update([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'salary' => $request->salary,
            'industry' => $request->industry,
            'type' => $request->type,
            'expiry_date' => $request->expiry_date,
        ]);

        return response()->json(['ok']);
    }
    public function apply($id, Request $request) {
        $user = User::where('token', $request->token)->first();
        $app = VacancyApplication::where([
            ['vacancy_id', $id],
            ['user_id', $user->id]
        ])->get(['id']);

        if ($app->count() == 0) {
            $saveData = VacancyApplication::create([
                'user_id' => $user->id,
                'vacancy_id' => $id,
            ]);
        }

        return response()->json(['ok']);
    }
}
