<?php

namespace App\Http\Controllers\Api\Restaurant;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Restaurant;

class RestaurantController extends Controller
{
    public function createRestaurant(Request $request) {
        $validator = Validator::make($request->all(),[
            'restaurant_name' => 'required',
            'restaurant_description' => 'required',
            'restaurant_address' => 'required',
            // 'midtrans_merchant_id' => 'required',
            // 'midtrans_client_key' => 'required',
            // 'midtrans_server_key' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 400);
        } else {
            Restaurant::create([
                "owner_id" => Auth::user()->id,
                'restaurant_name' => $request->restaurant_name,
                'restaurant_description' => $request->restaurant_description,
                'restaurant_address' => $request->restaurant_address,
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Restaurant Created Successfully.'
            ], 200);
        }
    }
    
}
