<?php

namespace App\Http\Controllers;

use App\Contact;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{

    public function dashboard()
    {
        $user = Contact::all()
        ->where('host', 'like', Auth::id());

        return response()->json(['teman' => $user]);
    }

    public function addFriend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $host = Auth::id();
        $friend = Contact::create([
            'host' => $host,
            'friends' => $request->get('id'),
        ]);

        return response()->json(['friend' => $friend], 201);
    }

    public function deleteFriend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $friend = $request->get('friend_id');

        DB::table('contact')
        ->where('friends', '=', $friend)
        ->delete();

        return response(['msg' => 'contact deleted'], 200);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cari' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $cari = $request->get('cari');

        $user = DB::table('users')
        ->where('name','like','%'.$cari.'%')
        ->first(['id', 'name', 'avatar'])
        ->paginate(5);

        if( $user == null ){
            return response(['msg' => 'not found'], 204);
        }

        return response()->json($user, 302);
    }

    public function isOnline(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'status' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->Json($validator->errors()->toJson(), 400);
        }

        $user = User::findOrFail(Auth::id());
        $status = $request->first('status');

        if( $status == 'online' ){
            $user->update([
                'is_online' => 1
            ]);
        }elseif( $status == 'offline'){
            $user->update([
                'is_online' => 0
            ]);
        }

        return response(['msg' => 'Status updated'], Response::HTTP_CONTINUE);
    }

}
