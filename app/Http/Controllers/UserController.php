<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Cookie;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function allUsers()
    {
        $data = User::all();
        return UserResource::collection($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5|max:255',
            'avatar' => 'image|mimes:jpg,png,jpeg',
            'phone' => 'required|string|min:8|max:15|unique:users',
            'email' => 'required|string|email|min:8|max:50|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'gender' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // $gender = $request->get('gender');

        // if( $gender == 'Cowokzs' ){
        //     $gender = 'M';
        // }elseif( $gender == 'Cewekzs' ){
        //     $gender = 'L';
        // }

        $user = User::findOrFail(Auth::id());
        if($request->hasFile('avatar')){
            Storage::delete(Auth::avatar());
            $avatar = $request->file('avatar')->store('avatars');

            $user->update([
                'name' => $request->get('name'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'gender' => $request->get('gender'),
                'password' => Hash::make($request->get('password')),
                'avatar' => 'storage/'.$avatar,
            ]);
        }elseif( $request->get('avatar') == null ){
            $user->update([
                'name' => $request->get('name'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'gender' => $request->get('gender'),
                'password' => Hash::make($request->get('password')),
                ]);
        }

        $token = JwTAuth::fromUser($user);

        $data = collect($user);
        $sent = $data->except('id', 'updated_at', 'created_at', 'email_verified_at');

        return response()->json($sent, $token);
    }

    public function destroy($id)
    {
        $data = User::findOrFail($id);
        $data->delete();

        return redirect('/');
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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5|max:255',
            'avatar' => 'image|mimes:jpg,png,jpeg|max:5120',
            'phone' => 'required|string|min:8|max:15|unique:users',
            'email' => 'required|string|email|min:8|max:50|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'gender' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // $gender = $request->get('gender');

        // if( $gender == 'Cowokzs' ){
        //     $gender = 'M';
        // }elseif( $gender == 'Cewekzs' ){
        //     $gender = 'L';
        // }

        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar')->store('avatars');

            $user = User::create([
                'name' => $request->get('name'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'gender' => $request->get('gender'),
                'password' => Hash::make($request->get('password')),
                'avatar' => 'storage/'.$avatar,
            ]);
        }elseif( $request->get('avatar') == null ){
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

        return response()->json($sent);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalideException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $data = collect($user);
        $sent = $data->except('id', 'updated_at', 'created_at', 'email_verified_at');

        return response()->json(compact('sent'));
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

}
