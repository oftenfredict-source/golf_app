<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HospitalityController extends Controller
{
    public function foodBeverage()
    {
        return view('hospitality.food-beverage');
    }

    public function counterManagement()
    {
        return view('hospitality.counter-management');
    }

    public function orders()
    {
        return view('hospitality.orders');
    }
}




