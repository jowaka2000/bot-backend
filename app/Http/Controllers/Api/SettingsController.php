<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(){
        $setting = Setting::find(1);

        if(!$setting){
            return response('Not Found',404);
        }

        return response(compact('setting'));
    }
}
