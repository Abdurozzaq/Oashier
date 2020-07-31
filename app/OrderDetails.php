<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = "order_details";

    protected $fillable = [
        'order_id',
        'menu_name',
        'total_price',
        'quantity'
    ];  
}
