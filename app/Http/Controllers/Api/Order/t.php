<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\RestaurantMenu;
use Illuminate\Support\Facades\Auth;

class CreateMenuOrderController extends Controller
{
    public function createOrder(Request $request) {
        $this->validate($request, [
            'menu_ids' => 'required',
        ]);

        // INPUT WITH DUPLICATE VALUE OF MENU ID
        $menu_ids = $request->menu_ids;
        // Return :
        // {
        //     "1": 1,
        //     "2": 1,
        //     "3": 2
        // }
        

        // Get Duplicate Count
        $menu_ids_count_dupl = array_count_values($menu_ids);
        // Return :
        // {
        //     "1": 1,
        //     "2": 1,
        //     "3": 2
        // }

        // Get Keys For Get Menus
        $menu_ids_count_dupl_keys = array_keys($menu_ids_count_dupl);
        // Return
        // [
        //     1,
        //     2,
        //     3
        // ]

        // Get Menu With Multiple Ids ( whereIn() )
        $menus = RestaurantMenu::whereIn('id', $menu_ids_count_dupl_keys)->get();

        // Combine each menu with qty of order
        foreach ($menus as $menu) {
            foreach ($menu_ids_count_dupl as $key => $value) {
                if ($menu->id == $key) {
                    $menu['menu_qty'] = $value;
                } 
                
            }
        }

        $data = [];


        // Get total price of each menu with their qty
        foreach ($menus as $menu) {
            $qty = $menu->menu_qty;
            $unit_price = $menu->menu_price;

            $data[] = array(
                'menu_name' => $menu->menu_name,
                'total_price' => $qty * $unit_price,
                'quantity' => $qty
            );
            
        }

        $total_prices = 0;

        foreach ($menus as $menu) {
            $total_prices = $total_prices + $menu->total_price;
        }

        Order::create([
            'user_id' => Auth::user()->id,
            'menu_ids' => $menu_ids,
            'total_price' => $total_prices,
            'details' => json_encode($data),
            'note' => $request->note
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order Created Successfully',
            'data' => $data
        ], 200);
        
        

    }
}
