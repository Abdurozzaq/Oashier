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
}
