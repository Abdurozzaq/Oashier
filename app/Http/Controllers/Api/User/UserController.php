<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function createUser(Request $request) {

        if ($request['role'] == 'admin') {

            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required|min:8',
                'role' => 'required'
            ]);

        } else {

            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'restaurant_name' => 'required',
                'address' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required|min:8',
                'role' => 'required'
            ]);

        }
       

        if ($request['role'] == 'admin') {

            $user = User::create([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ])->assignRole('admin');

        } else {

            $user = User::create([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'restaurant_name' => $request['restaurant_name'],
                'email' => $request['email'],
                'address' => $request['address'],
                'password' => Hash::make($request['password']),
            ])->assignRole('cashier');

        }

        $user->sendApiEmailVerificationNotification();

        return response()->json([
            'status' => 'success',
            'message' => 'Please confirm the new user account email address by clicking on verify user button sent to you on his email'
        ], 200);
    }
}
