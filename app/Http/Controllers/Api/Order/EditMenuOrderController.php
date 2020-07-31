<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use Illuminate\Support\Facades\Auth;

class EditMenuOrderController extends Controller
{
  public function getOrderList() {

    $data = Order::where('user_id', Auth::user()->id)->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Order Received Successfully',
        'data' => $data
    ], 200);
  }

  public function editOrder(Request $request) {

    $this->validate($request, [
      'order_id' => 'required',
      'order_note' => 'required',
      'order_buyer_name' => 'required',
    ]);

    $order = Order::findOrFail($request->order_id);
    $order->order_note = $request->order_note;
    $order->order_buyer_name = $request->order_buyer_name;
    $order->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Order Information Updated Successfully',
    ], 200);
  }
}
