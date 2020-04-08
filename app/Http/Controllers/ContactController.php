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
        $contact = DB::table('contact')
        ->where('host',Auth::id())
        ->join('users', 'users.id', '=', 'contact.friends')
        ->select('users.id','name')
        ->get();

        return response()->json($contact);
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
        Contact::create([
            'host' => $host,
            'friends' => $request->get('id'),
        ]);

        return response()->json(['msg' => 'friend added'], 201);
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

        // $cek = DB::table('contact')->pluck('id');
        // foreach($cek as $c){
        //     if ( $friend != $c){
        //         return response(['msg' => 'contact is not exist'], 400);
        //     }
        // }
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
        ->where('name','like', '%'.$cari.'%')
        ->get(['id', 'name', 'avatar'])
        ->toArray();

        if( $user == null ){
            return response(['msg' => 'not found'], 204);
        }

        return response()->json($user, 302);
    }

}
