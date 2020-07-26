<?php

namespace App\Http\Controllers\Api\RestaurantMenu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RestaurantMenu;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RestaurantMenuController extends Controller
{
  public function test() {
    $data = RestaurantMenu::get();

    return response()->json([
        'status' => 'success',
        'data' => $data
    ], 200);
  }

  public function showMenu() {
    $data = RestaurantMenu::where('user_id', Auth::user()->id)->get();

    return response()->json([
      'status' => 'success',
      'menu' => $data
    ], 200);
  }

  public function deleteMenu(Request $request) {
    $menuId = $request->menuId;

    $menu = RestaurantMenu::findOrFail($menuId);
    $menu->delete();

    return response()->json([
      'status' => 'success',
      'message' => 'Menu has been deleted Successfully'
    ], 200);
  }

  public function getMenu(Request $request) {
    $menuId = $request->menuId;

    $menu = RestaurantMenu::findOrFail($menuId);

    return response()->json([
      'status' => 'success',
      'data' => $menu
    ], 200);
  }

  public function createMenu(Request $request) {
    $this->validate($request, [
        'menu_name' => 'required',
        'menu_description' => 'required',
        'menu_price' => 'required',
        'menu_availability' => 'required',
        'menu_picture' => 'required|file|mimes:jpeg,png,jpg'
    ]);
    if($request->hasFile('menu_picture')){
      $resource = $request->file('menu_picture');
      $name = Carbon::now()->timestamp."_".$resource->getClientOriginalName();
      $userId = Auth::user()->id;
      $url = "/storage/menu_picture/".$userId."/".$name;
      $resource->move(\base_path() ."/public/storage/menu_picture/".$userId, $name);
      
      if ($request->menu_stock_qty) {
        RestaurantMenu::create([
          'user_id' => Auth::user()->id,
          'menu_name' => $request->menu_name,
          'menu_description' => $request->menu_description,
          'menu_price' => $request->menu_price,
          'menu_availability' => $request->menu_availability,
          'menu_picture' => $url,
          'menu_stock_qty' => $request->menu_stock_qty
        ]);
      } else {
        RestaurantMenu::create([
          'user_id' => Auth::user()->id,
          'menu_name' => $request->menu_name,
          'menu_description' => $request->menu_description,
          'menu_price' => $request->menu_price,
          'menu_availability' => $request->menu_availability,
          'menu_picture' => $url
        ]);
      }
      

      return response()->json([
        'status' => 'success',
        'message' => 'Menu Created Successfully'
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Image Not Found'
      ], 400);
    }
  }


  public function editMenu(Request $request) {
    $this->validate($request, [
        'menu_name' => 'required',
        'menu_description' => 'required',
        'menu_price' => 'required',
        'menu_availability' => 'required',
    ]);
    if($request->hasFile('menu_picture')) {
      $resource = $request->file('menu_picture');
      $name = Carbon::now()->timestamp."_".$resource->getClientOriginalName();
      $userId = Auth::user()->id;
      $url = "/storage/menu_picture/".$userId."/".$name;
      $resource->move(\base_path() ."/public/storage/menu_picture/".$userId, $name);

      $theMenu = RestaurantMenu::findOrFail($request->menu_id);
      $theMenu->menu_name = $request->menu_name;
      $theMenu->menu_description = $request->menu_description;
      $theMenu->menu_price = $request->menu_price;
      $theMenu->menu_availability = $request->menu_availability;
      $theMenu->menu_picture = $url;
      $theMenu->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Menu Edited Successfully'
      ], 200);
    } else {
      $theMenu = RestaurantMenu::findOrFail($request->menu_id);
      $theMenu->menu_name = $request->menu_name;
      $theMenu->menu_description = $request->menu_description;
      $theMenu->menu_price = $request->menu_price;
      $theMenu->menu_availability = $request->menu_availability;
      $theMenu->save();

      return response()->json([
        'status' => 'success',
        'message' => 'Menu Edited Successfully'
      ], 200);
    }
  }

  public function editStockQty(Request $request) {
    $this->validate($request, [
      'menu_stock_qty' => 'required',
    ]);
  
    $menu = RestaurantMenu::findOrFail($request->menu_id);
    $menu->menu_stock_qty = $request->menu_stock_qty;
    $menu->save();
  
    return response()->json([
      'status' => 'success',
      'message' => 'Menu Stock Quantity Edited Successfully'
    ], 200);
  }
  
}

