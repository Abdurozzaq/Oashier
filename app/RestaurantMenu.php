<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenu extends Model
{
    // The Table of this model
    protected $table = "restaurant_menus";

    protected $fillable = [
        'user_id',
        'menu_name',
        'menu_description',
        'menu_price',
        'menu_availability',
        'menu_picture',
        'menu_stock_qty'
    ];

}
