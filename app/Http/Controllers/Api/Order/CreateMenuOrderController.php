<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\RestaurantMenu;

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

        $menu_ids_count_dupl_vals = array_values($menu_ids_count_dupl);

        $menus = RestaurantMenu::whereIn('id', $menu_ids_count_dupl_keys)->get();
            
        // foreach ($menus as $menu) {
        //     for ($i = 0; $i < count($menu_ids_count_dupl_vals); $i++) {
        //         $menu['menu_qty'] = $menu_ids_count_dupl_vals[$i];
        //     }
            
        // }

            
        foreach ($menus as $menu) {
            foreach ($menu_ids_count_dupl as $key => $value) {
                if ($menu->id == $key) {
                    $menu['menu_qty'] = $value;
                } 
                
            }
            
        }

        return $menus;
        
        // Order::create({
        //     'user_id' => Auth::user()->id,
        //     'menu_ids' => $menu_ids,

        // })

    }
}
