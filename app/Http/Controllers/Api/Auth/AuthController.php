<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function refreshToken() {
        $user = Auth::user();

        if($user->hasRole('admin')) {
            $abilities = [
                'user:create',
                'user:update',
                'user:delete',
                'user:index',
            ];
        } else {
            $abilities = [
                'user:perms'
            ];
        }

        return response()->json([
            'status' => 'Success',
            //LALU PADA METHOD createToken(), TAMBAHKAN PARAMETER ABILITIESNYA
            'token' => $user->createToken($user->roles->pluck('name'), $abilities)->plainTextToken,
        ], 200);
    }

    public function getUser(Request $request){
        return response()->json([
            'status' => 'Success',
            //LALU PADA METHOD createToken(), TAMBAHKAN PARAMETER ABILITIESNYA
            'user' => Auth::user(),
            'role' => Auth::user()->roles->pluck('name'),
        ], 200);
    }

    public function logoutCurrentUser() {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'Success',
            'message' => 'Token Deleted / Logged Out'
        ], 200);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'failed', 'errors' => [ 'password' => ['Your Password is Incorrect'] ] ], 500);
        } else {
            if($user->hasRole('admin')) {
                $abilities = [
                    'user:create',
                    'user:update',
                    'user:delete',
                    'user:index',
                ];
            } else {
                $abilities = [
                    'user:perms'
                ];
            }

            if($user->email_verified_at !== NULL){
                return response()->json([
                    'status' => 'Success',
                    //LALU PADA METHOD createToken(), TAMBAHKAN PARAMETER ABILITIESNYA
                    'token' => $user->createToken($user->roles->pluck('name'), $abilities)->plainTextToken,
                    'role' => $user->roles->pluck('name'),
                ], 200);
            } else {
                return response()->json([
                    'status' => 'Failed',
                    'code' => 'unverified-email',
                    'errors'=> ['email' => ['Please Verify Email'] ],
                ], 401);
            }


        }


    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 401);
        } else {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole('user');

            $user->sendApiEmailVerificationNotification();

            return response()->json([
                'status' => 'Success',
                'message' => 'Please confirm yourself by clicking on verify user button sent to you on your email'
            ], 200);
        }
    }
    
}