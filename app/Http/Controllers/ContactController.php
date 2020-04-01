<?php

namespace App\Http\Controllers;

use App\Contact;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{

    public function dashboard()
    {

    }

    public function addFriend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'id' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $contact = User::findOrFail($request->get('id'));
        $host = Auth::id();

        $friend = Contact::create([
            'host' => $host,
            'friends' => $contact,
        ]);

        return response()->json(['friend' => $friend], 201);
    }

    public function deleteFriend()
    {

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
        ->where('name','like',"%".$cari."%")
        ->first(['id', 'name', 'avatar']);

        if( $user == null ){
            return response(['msg' => 'not found'], 204);
        }

        return response()->json($user, 302);
    }

    public function isOnline()
    {

    }

}
