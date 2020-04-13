<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use App\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5|max:255',
            'avatar' => 'image|mimes:jpg,png,jpeg|max:4096',
            'phone' => 'required|string|min:8|max:15|unique:users',
            'email' => 'required|string|email|min:8|max:50|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'gender' => 'required|alpha|max:1',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if($request->hasFile('avatar')){
            if($request->file('avatar')->isValid()){
                $avatar = base64_encode(file_get_contents($request->file('avatar')));
                $user = User::create([
                    'name' => $request->get('name'),
                    'phone' => $request->get('phone'),
                    'email' => $request->get('email'),
                    'gender' => $request->get('gender'),
                    'password' => Hash::make($request->get('password')),
                    'avatar' => $avatar,
                ]);
            }
        }elseif( $request->file('avatar') == null ){
            $user = User::create([
                'name' => $request->get('name'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'gender' => $request->get('gender'),
                'password' => Hash::make($request->get('password')),
                ]);
        }

        $data = collect($user);
        $sent = $data->except('id', 'updated_at', 'created_at', 'email_verified_at');

        return response()->json(['data' => $sent], 201);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5|max:255',
            'avatar' => 'image|mimes:jpg,png,jpeg|max:4096',
            'phone' => 'required|string|min:8|max:15|unique:users',
            'email' => 'required|string|email|min:8|max:50|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'gender' => 'required|alpha|max:1',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::findOrFail(Auth::id());

        if($request->hasFile('avatar')){
            if($request->file('avatar')->isValid()){
                $avatar = base64_encode(file_get_contents($request->file('avatar')));
                $user->update([
                    'name' => $request->get('name'),
                    'phone' => $request->get('phone'),
                    'email' => $request->get('email'),
                    'gender' => $request->get('gender'),
                    'password' => Hash::make($request->get('password')),
                    'avatar' => base64_decode($avatar),
                ]);
            }
        }elseif( $request->get('avatar') == null ){
            $user->update([
                'name' => $request->get('name'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'gender' => $request->get('gender'),
                'password' => Hash::make($request->get('password')),
                ]);
        }

        $data = collect($user);
        $sent = $data->except('id', 'updated_at', 'created_at', 'email_verified_at');

        return response()->json(['data' => $sent], Response::HTTP_ACCEPTED);
    }

    public function destroy()
    {
        $data = User::findOrFail(Auth::id());
        $data->delete();

        return response()->json(['msg' => 'user deleted'], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $data = collect($user);
        $sent = $data->except('id', 'updated_at', 'created_at', 'email_verified_at');

        return response()->json(['data' => $sent], Response::HTTP_ACCEPTED);
        }

    public function userOnlineStatus()
    {
        $users = User::all();

        foreach ($users as $user) {
            if(Cache::has('user-is-online-' . $user->id))
            echo $user->name . ' is online. Last Seen: '. Carbon::parse($user->last_seen)->diffForHumans() . " <br>";
            else
            echo $user->name . ' is offline. Last Seen: '. Carbon::parse($user->last_seen)->diffForHumans() . " <br>";
        }
    }

    public function logout()
    {
        try {
            config([
                'jwt.blacklist_enabled' =>  true
            ]);

            \Cookie::forget('token');
            \Cache::forget('token');
            Auth()->logout();
            JWTAuth::invalidate(JWTAuth::parseToken());

            return response()->json([
                'success' => true,
                'message' => 'User telah berhasil logout'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Anu~~ Usernya gak bisa logout itu...'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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

        $status = $request->get('status');

        if( $status == 'online' ){
            $user->update([
                'is_online' => 1
            ]);
            return response()->json(['msg' => 'status changed to online'], 200);
        }elseif( $status == 'offline'){
            $user->update([
                'is_online' => 0
            ]);
            return response()->json(['msg' => 'status changed to offline'], 200);
        }

    }

}
