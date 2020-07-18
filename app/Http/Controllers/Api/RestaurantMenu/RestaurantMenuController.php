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
    $data = RestaurantMenu::with('stockMenu')->get();

    return response()->json([
        'status' => 'success',
        'data' => $data
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
      $resource->move(\base_path() ."/public/storage/menu_picture/".$userId, $name);

      RestaurantMenu::create([
        'user_id' => Auth::user()->id,
        'menu_name' => $request->menu_name,
        'menu_description' => $request->menu_description,
        'menu_price' => $request->menu_price,
        'menu_availability' => $request->menu_availability,
        'menu_picture' => $name
      ]);

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
}
