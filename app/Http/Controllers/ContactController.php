<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
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
        ->get()->toArray();

        if ( $contact == null ){
            return response()->json(['msg' => 'no friend found. even a single one! poor you boy~']);
        }
        return response()->json($contact);
    }

    public function addFriend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $host = Auth::id();
        $friend = $request->get('friend_id');

        $test1 = DB::table('contact')
        ->where('host', Auth::id())->where('friends','like', '%'.$friend.'%')
        ->first('friends');
        $collection = collect($test1)->contains($friend);
        $test2 = DB::table('users')
        ->where('id','like', '%'.$friend.'%')
        ->get('id')->toArray();

        if( $collection == true){
            return response()->json(['msg' => 'friend already added'], 400);
        }elseif( $host == (int)$friend){
            return response()->json(['msg' => 'you cant added yourself as friend.. poor of you, buddy~'], 400);
        }elseif( $test2 == null ){
            return response()->json(['msg' => 'theres no users with that id.. Damn!'], 400);
        }

        Contact::create([
            'host' => $host,
            'friends' => $request->get('friend_id'),
        ]);
        Contact::create([
            'host' => $request->get('friend_id'),
            'friends' => $host,
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
        $test = Contact::all()
        ->where('friends', $friend)->where('host', Auth::id())
        ->toArray();
        if ($test == null){
            abort(401);
        }

        DB::table('contact')
        ->where('friends', $friend)->where('host', Auth::id())
        ->delete();

        DB::table('contact')
        ->where('host', $friend)->where('friends', Auth::id())
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
