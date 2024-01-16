<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Notifications\InterviewInvitation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function list(Request $request) {
        $now = Carbon::now();
        $user = User::where('token', $request->token)->first();

        $appointments = [
            'employee' => Appointment::where([
                ['employee_id', $user->id],
                ['dues', '>=', $now->format('Y-m-d H:i:s')]
            ])
            ->orderBy('is_accepted_by_employee', 'ASC')
            ->with('employer')->get(),
            'employer' => Appointment::where([
                ['employer_id', $user->id],
                ['dues', '>=', $now->format('Y-m-d H:i:s')]
            ])
            ->orderBy('is_accepted_by_employee', 'ASC')
            ->with('employee')->get()
        ];

        return response()->json([
            'appointments' => $appointments
        ]);
    }
    public function store($username, Request $request) {
        $employer = User::where('token', $request->token)->first();
        $employee = User::where('username', $username)->first();

        $theDues = $request->dues . " " . $request->time;

        $saveData = Appointment::create([
            'employer_id' => $employer->id,
            'employee_id' => $employee->id,
            'is_accepted_by_employee' => null,
            'dues' => $theDues,
            'notes' => $request->notes,
        ]);

        $employee->notify(new InterviewInvitation([
            'appointment' => $saveData,
            'employee' => $employee,
            'employer' => $employer,
        ]));

        return response()->json([
            'status' => 200,
        ]);
    }
    public function acceptInvitation(Request $request) {
        $data = Appointment::where('id', $request->id);
        $appointment = $data->first();
        $user = User::where('token', $request->token)->first();

        if ($appointment->employee_id == $user->id) {
            $data->update([
                'is_accepted_by_employee' => true,
            ]);
        }
        
        return response()->json([
            'status' => 200,
        ]);
    }
    public function sendLink(Request $request) {
        $data = Appointment::where('id', $request->id);
        $data->update([
            'link' => $request->link,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
}
