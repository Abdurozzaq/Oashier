<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\RestaurantMenu;
use App\OrderDetails;
use Illuminate\Support\Facades\Auth;

class CreateMenuOrderController extends Controller
{
    public function createOrder(Request $request) {
        Order::create([
            'user_id' => Auth::user()->id,
            'order_buyer_name' => $request->order_buyer_name,
            'order_note' => $request->order_note
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order Created Successfully',
        ], 200);
    }

    public function createOrderDetails(Request $request) {
        Order::create([
            'order_id' => $request->order_id,
            'menu_name' => $request->menu_name,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price

        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order Details Created Successfully',
        ], 200);
    }
}
