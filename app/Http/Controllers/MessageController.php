<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function showMessage($friend_id)
    {
        $user = Auth::id();

        $msg = Message::collect($user)
        ->all()
        ->users();

        dd($msg);
    }

    public function sendMessage(Request $request, $friend_id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $host = Auth::id();

        $msg = Message::create([
            'from_id' => $host,
            'to_id' => $friend_id,
            'content' => $request->get('content')
        ]);

        return response()->json(['message' => $msg], 200);
    }

    public function editMessage(Request $request, $msg_id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $message = Message::findOrFail($msg_id);
        $message->update([
            'content' => $request->get('content')
        ]);

        return response()->json(['message' => $message], 200);
    }

    public function isRead(Request $request, $msg_id)
    {
        $validator = Validator::make($request->all(),[
            'read' => 'required|boolean',
        ]);

        if($validator->fails()){
            return response()->Json($validator->errors()->toJson(), 400);
        }

        $user = Message::findOrFail($msg_id);
        $read = $request->first('read');

        $user->update([
            'is_read' => $read
        ]);

        return response(['msg' => 'Status updated'], Response::HTTP_CONTINUE);
    }

    public function deleteMessage($msg_id)
    {
        $data = Message::findOrFail($msg_id);
        $data->delete();

        return response()->json(['msg' => 'Message Deleted'], Response::HTTP_ACCEPTED);
    }
}
