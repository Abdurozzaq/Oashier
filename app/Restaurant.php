<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    // The Table of this model
    protected $table = "restaurant";

    protected $fillable = [
        'owner_id',
        'restaurant_name',
        'restaurant_description',
        'restaurant_address',
        'midtrans_merchant_id',
        'midtrans_client_key',
        'midtrans_server_key',
    ];
}
