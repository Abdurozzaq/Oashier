<?php

use Illuminate\Database\Seeder;
use App\RestaurantMenu;
use Illuminate\Support\Facades\Auth;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RestaurantMenu::create([
            'user_id' => 2,
            'menu_name' => 'Ayam Bakar',
            'menu_description' => 'Sepotong Ayam Bakar + Sambal',
            'menu_price' => '25000',
            'menu_availability' => 'Yes',
            'menu_picture' => null,
            'menu_stock_qty' => 10
        ]);

        RestaurantMenu::create([
            'user_id' => 2,
            'menu_name' => 'Ikan Bakar',
            'menu_description' => 'Sepotong Ikan Bakar + Sambal',
            'menu_price' => '25000',
            'menu_availability' => 'Yes',
            'menu_picture' => null,
            'menu_stock_qty' => 10
        ]);

        RestaurantMenu::create([
            'user_id' => 2,
            'menu_name' => 'Nasi',
            'menu_description' => 'Nasi',
            'menu_price' => '5000',
            'menu_availability' => 'Yes',
            'menu_picture' => null,
            'menu_stock_qty' => 20
        ]);
    }
}
