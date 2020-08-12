<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";

    protected $fillable = [
        'user_id',
        'order_number',
        'order_note',
        'customer_nominal',
        'change_money',
        'is_cancelled',
        'is_paid'
    ];

}
