<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {


        return view('dashboard', [
            // 'vehiclesCount' => $vehiclesCount,
            // 'servicesCount' => $servicesCount,
            // 'usersCount' => $usersCount,
            // 'exceptionalClosures' => $exceptionalClosures,
            // 'exceptionalEvents' => $exceptionalEvents,
            // 'teamMembers' => $teamMembers,
            // 'jobOffers' => $jobOffers,
        ]);
    }
}
