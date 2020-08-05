<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use DB;
use App\RestaurantMenu;
use App\OrderDetails;
use Illuminate\Support\Facades\Auth;

class OrderDetailsController extends Controller
{

    public function createOrderDetails(Request $request) {
        $data = $request->all();
        
        foreach ($data as $item) {

            OrderDetails::updateOrCreate(['code' => $item['code']], $item);
        
        };

        return response()->json([
            'status' => 'success',
            'message' => 'Order Details Created Or Updated Successfully',
        ], 200);
    }

    public function getOrderDetailsList($id) {

        $menus = DB::table('order_details')
                    ->where('order_id', $id)
                    ->join('restaurant_menus', 'restaurant_menus.id', '=', 'order_details.menu_id')
                    ->select('order_details.*', 'restaurant_menus.menu_stock_qty')
                    ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Order Details Received Successfully',
            'menu' => $menus
        ], 200);
    }

    public function deleteMenuFromOrder($code) {
        $menu = OrderDetails::where('code', $code)->get()->each->delete();
    
        return response()->json([
          'status' => 'success',
          'message' => 'Menu Deleted Successfully From Order',
        ], 200);
      }
}
