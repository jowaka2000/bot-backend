<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Schedule;
// use FacebookAds\Api;

use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class TestController extends Controller
{

    public function index()
    {

        $schedule = Schedule::find(1);

        
    }


}
