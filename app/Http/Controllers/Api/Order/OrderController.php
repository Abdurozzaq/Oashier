<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\OrderDetails;
use App\RestaurantMenu;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{

  public function createOrder(Request $request) {
    $order = Order::create([
        'user_id' => Auth::user()->id,
        'order_number' => $request->order_number,
        'order_note' => $request->order_note
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Order Created Successfully',
        'id' => $order->id
    ], 200);
  }

  public function getOrderList() {

    $data = Order::where('user_id', Auth::user()->id)->where('is_cancelled', 0)->where('is_paid', 0)->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Order Received Successfully',
        'data' => $data
    ], 200);
  }

  public function getCancelledOrderList() {

    $data = Order::where('user_id', Auth::user()->id)->where('is_cancelled', 1)->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Cancelled Order Received Successfully',
        'data' => $data
    ], 200);
  }

  public function getSuccessOrderList() {

    $data = Order::where('user_id', Auth::user()->id)->where('is_paid', 1)->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Successed Order Received Successfully',
        'data' => $data
    ], 200);
  }

  public function getOrderNumber() {



    $today = Carbon::now()->toDateTimeString();

    $theOrder = Order::where('user_id', Auth::user()->id)->whereDate('created_at', '=', date('Y-m-d'))->get();

    if (count($theOrder) > 0) {
      $order_number = count($theOrder) + 1;
    } else {
      $order_number = 1;
    }



    return response()->json([
        'status' => 'success',
        'message' => 'A Safe New Order Number Received Successfully',
        'order_number' => $order_number
    ], 200);
  }

  public function editOrder(Request $request) {

    $this->validate($request, [
      'order_id' => 'required',
      'order_note' => 'required',
      'order_number' => 'required',
    ]);

    $order = Order::findOrFail($request->order_id);
    $order->order_note = $request->order_note;
    $order->order_number = $request->order_number;
    $order->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Order Information Updated Successfully',
    ], 200);
  }

  public function cancelOrder($id) {

    // First Delete The Order From Orders Table
    $order = Order::findOrFail($id);
    $order->is_cancelled = 1;
    $order->save();

    $orderDetails = OrderDetails::where('order_id', $id)->get();

    foreach ($orderDetails as $od) {
      $menu = RestaurantMenu::where('id', $od->menu_id)->first();
      $menu->menu_stock_qty = $menu->menu_stock_qty + $od->quantity;
      $menu->save();
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Order Cancelled Successfully && Menu Stock On Order Returned Back.',
    ], 200);
  }

  public function payCash(Request $request, $id) {

    $this->validate($request, [
      'customerNominal' => 'required',
      'changeMoney' => 'required',
    ]);

    $order = Order::findOrFail($id);
    $order->is_paid = 1;
    $order->customer_nominal = $request->customerNominal;
    $order->change_money = $request->changeMoney;
    $order->save();

    return response()->json([
      'status' => 'success',
      'message' => 'Order Transaction has been Successful.',
    ], 200);
  }
}
