<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class IdentityController extends Controller
{
    public function editIdentity(Request $request) {

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'restaurant_name' => 'required',
        ]);

        $user = User::findOrFail(Auth::user()->id);
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->restaurant_name = $request['restaurant_name'];
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Identity Changed Successfully',
        ], 200);
        
    }
}
