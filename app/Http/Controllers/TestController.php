<?php

namespace App\Http\Controllers;

use App\Actions\PostOnFacebookAction;
use App\Models\App;
use App\Models\Fail;
use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index(){

        $schedule = Schedule::find(14);



        $history = $schedule->history;
        $history= json_decode($history,true);
        dd($history['last image index posted']);

    }


}
