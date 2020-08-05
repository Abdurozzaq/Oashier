<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = "order_details";

    protected $fillable = [
        'order_id',
        'menu_id',
        'code',
        'menu_name',
        'one_unit_price',
        'total_price',
        'quantity'
    ];  

}
