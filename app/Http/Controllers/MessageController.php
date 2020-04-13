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

        $test = DB::table('contact')
        ->where('host', Auth::id())->where('friends','like', '%'.$friend_id.'%')
        ->first('friends');
        $collection = collect($test)->contains($friend_id);
        if( $collection == false ){
            return response()->json(['msg' => 'not a friend'], 401);
        }

        $msg = DB::table('messages')
        ->where('from_id', $user)->where('to_id', $friend_id)
        ->orWhere('from_id', $friend_id)->where('to_id', $user)
        ->orderBy('created_at', 'asc')->get();

        return response()->json($msg, 200);
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

        $test = DB::table('contact')
        ->where('host', Auth::id())->where('friends','like', '%'.$friend_id.'%')
        ->first('friends');
        $collection = collect($test)->contains($friend_id);
        if( $collection == false ){
            abort(401);
        }

        $msg = Message::create([
            'from_id' => $host,
            'to_id' => $friend_id,
            'content' => $request->get('content')
        ]);

        $data = collect($msg);
        $sent = $data->except('id', 'updated_at');
        return response()->json(['msg' => $sent], 200);
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

    public function deleteMessage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'msg_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->Json($validator->errors()->toJson(), 400);
        }

        $msg_id = $request->get('msg_id');
        $user = Auth::id();

        $test = DB::table('messages')
        ->where('id', $msg_id)->where('from_id', $user)
        ->get();
        if( $test == null ){
            return response()->json(['msg' => 'cannot delete message'], 400);
        }

        $data = Message::findOrFail($msg_id);
        $data->delete();

        return response()->json(['msg' => 'Message Deleted'], Response::HTTP_ACCEPTED);
    }
}
