<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockMenu extends Model
{
    // The Table of this model
    protected $table = "stock_menus";

    protected $fillable = [
        'menu_id',
        'quantity',
    ];

    public function restaurantMenu()
    {
    	return $this->belongsTo('App\RestaurantMenu');
    }
}
