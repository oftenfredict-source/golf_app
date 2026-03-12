<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GolfServicesController extends Controller
{
    /**
     * Show driving range page
     */
    public function drivingRange()
    {
        return view('golf-services.driving-range');
    }

    /**
     * Show equipment rental page
     */
    public function equipmentRental()
    {
        return view('golf-services.equipment-rental');
    }

    /**
     * Show ball management page
     */
    public function ballManagement()
    {
        return view('golf-services.ball-management');
    }
}




