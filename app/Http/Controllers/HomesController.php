<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class HomesController extends Controller
{
    public function __construct()
    {

        $this->middleware(['auth']);
    }
    public function index(){
        $setting  = Setting::find(1);

        return view('home.index',compact('setting'));
    }



    

}
