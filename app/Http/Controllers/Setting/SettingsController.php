<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function storeFacebookProfile(Request $request)
    {
        $this->validate($request, [
            'profile_link' => 'required',
            'profile_name' => 'required',
        ]);


        $settings = Setting::find(1);

        if ($settings) {
            //update

            $settings->update(['facebook_profile_link' => $request->profile_link, 'facebook_profile_names' => $request->profile_name]);

            return back()->with('message', 'success');
        }


        Setting::create(['facebook_profile_link' => $request->profile_link, 'facebook_profile_names' => $request->profile_name]);

        return back()->with('message', 'success');
    }
}
