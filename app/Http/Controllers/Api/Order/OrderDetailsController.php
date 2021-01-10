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

            $od = OrderDetails::where('code', $item['code'])->first();

            if ($od === null) {
                OrderDetails::create([
                    'order_id' => $item['order_id'],
                    'menu_id' => $item['menu_id'],
                    'menu_name' => $item['menu_name'],
                    'code' => $item['code'],
                    'quantity' => $item['quantity'],
                    'one_unit_price' => $item['one_unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                $menu = RestaurantMenu::where('id', $item['menu_id'])->first();
                $menu->menu_stock_qty = $menu->menu_stock_qty - $item['quantity'];
                $menu->save();
            }

            if ($od !== null) {
                if ($od->quantity < $item['quantity']) {
                    $menu = RestaurantMenu::where('id', $item['menu_id'])->first();
                    $difference = $item['quantity'] - $od->quantity;
                    $menu->menu_stock_qty = $menu->menu_stock_qty - $difference;
                    $menu->save();

                    $od->total_price = $item['total_price'];
                    $od->save();
                }

                if ($od->quantity > $item['quantity']) {
                    $menu = RestaurantMenu::where('id', $item['menu_id'])->first();
                    $difference = $od->quantity - $item['quantity'];
                    $menu->menu_stock_qty = $menu->menu_stock_qty + $difference;
                    $menu->save();

                    $od->total_price = $item['total_price'];
                    $od->save();
                }

                $od->quantity = $item['quantity'];
                $od->save();
            }

            // OrderDetails::updateOrCreate(['code' => $item['code']], $item);
            
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

    public function getCancelledOrderDetailsList($id) {

        $menus = DB::table('order_details')
                    ->where('order_id', $id)
                    ->get();

        $total_price_all = 0;        

        foreach ($menus as $menu) {
            $total_price_all = $total_price_all + $menu->total_price;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order Details Received Successfully',
            'menu' => $menus,
            'total' => $total_price_all
        ], 200);
    }

    public function getSuccessOrderDetailsList($id) {

        $menus = DB::table('order_details')
                    ->where('order_id', $id)
                    ->get();

        $total_price_all = 0;        

        foreach ($menus as $menu) {
            $total_price_all = $total_price_all + $menu->total_price;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order Details Received Successfully',
            'menu' => $menus,
            'total' => $total_price_all
        ], 200);
    }

    public function deleteMenuFromOrder($code) {
        $orderDetails = OrderDetails::where('code', $code)->first();

        if ($orderDetails == null || $orderDetails == '') {

          return response()->json([
            'status' => 'successNoMenuSaved',
            'message' => 'Menu is not saved, delete it from client!',
          ], 200);

        } else {

          $menu = RestaurantMenu::where('id', $orderDetails->menu_id)->first();
          $menu->menu_stock_qty = $menu->menu_stock_qty + $orderDetails->quantity;
          $menu->save();

          $orderDetails->delete();
      
          return response()->json([
            'status' => 'success',
            'message' => 'Menu Deleted Successfully From Order',
          ], 200);

        }

      }
}
