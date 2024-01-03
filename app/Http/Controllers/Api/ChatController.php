<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function load(Request $request) {
        // loading chats
        $user = User::where('token', $request->token)->first();

        $chats = Chat::where([
            ['sender_id', $user->id],
            ['receiver_id', $request->target_id],
        ])->orWhere([
            ['receiver_id', $user->id],
            ['sender_id', $request->target_id],
        ])
        ->orderBy('created_at', 'DESC')
        ->with(['sender', 'receiver'])
        ->paginate(25);

        return response()->json([
            'chats' => $chats,
        ]);
    }
    public function send(Request $request) {
        $user = User::where('token', $request->token)->first();
        $receiver = User::where('username', $request->receiver_username)->first();
        $type = $request->type;

        $toSave = [
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'type' => $type,
        ];

        if ($type == "text") {
            $toSave['body'] = $request->body;
        } else if ($type == "image") {
            $image = $request->file('body');
            $imageFileName = $image->getClientOriginalName();
            $toSave['body'] = $imageFileName;
            $image->storeAs('public/chat_images', $imageFileName);
        } else if ($type == "file") {
            $file = $request->file('body');
            $fileName = $file->getClientOriginalName();
            $toSave['body'] = $fileName;
            $file->storeAs('public/chat_files', $fileName);
        }

        $saveData = Chat::create($toSave);

        return response()->json([
            'status' => 200,
        ]);
    }
    public function room(Request $request) {
        // get room
        $user = User::where('token', $request->token)->first();
        
        $roomsRaw = Chat::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)
        ->latest('created_at')
        ->with(['sender', 'receiver'])
        ->get()
        ->groupBy(function ($item) {
            return $item->sender_id < $item->receiver_id ?
                $item->sender_id . '-' . $item->receiver_id :
                $item->receiver_id . '-' . $item->sender_id;
        });

        $rooms = [];
        foreach ($roomsRaw as $room) {
            $latestMessage = $room->first();
            $toAssign = [
                'id' => $latestMessage->id,
                'sender_id' => $latestMessage->sender_id,
                'receiver_id' => $latestMessage->receiver_id,
                'body' => $latestMessage->body,
                'type' => $latestMessage->type,
                'created_at' => $latestMessage->created_at,
            ];

            if ($latestMessage->sender_id == $user->id) {
                $toAssign['user'] = $latestMessage->receiver;
            } else {
                $toAssign['user'] = $latestMessage->sender;
            }
            
            $rooms[] = $toAssign;
        }

        return response()->json([
            'rooms' => $rooms,
        ]);
    }
    public function delete(Request $request) {
        $data = Chat::where('id', $request->id);
        $chat = $data->first();

        $deleteData = $data->delete();
        if ($chat->type == "image") {
            $delImage = Storage::delete('public/chat_images/' . $chat->body);
        }
        
        return response()->json([
            'status' => 200,
        ]);
    }
}
