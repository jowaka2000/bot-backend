<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAppStoreRequest;
use App\Models\App;
use App\Models\User;
use Illuminate\Http\Request;

class UserAppsController extends Controller
{

    public function index(Request $request)
    {

        $user = $request->user();

        /** @var User $user */

        if (!$user) {
            return response('Your data is not found', 499);
        }


        $apps = App::allApps($user->id);

        return response(compact('apps'));
    }


    public function store(UserAppStoreRequest $request)
    {
        $data = $request->validated();


        $user = $request->user();

        /** @var User $user */

        if (!$user) {
            return response('No user found', 499);
        }


        $timestamp = now()->format('YmdHis');
        $randomNumber = mt_rand(1000, 9999);

        $customId = $timestamp . $randomNumber;

        $customId = encrypt($customId);
        $hashedCustomId = hash('sha256', $customId);



        $app = $user->apps()->create([
            'bot_type' => $data['botType'],
            'bot_name' => $data['botName'],
            'media_name' => $data['mediaName'],
            'page_id' => $data['pageID'],
            'bot_user_id' => $data['userID'],
            'bot_nickname' => $data['botNickname'],
            'channel_link' => $data['channelLink'],
            'bot_username' => $data['botUsername'],
            'bot_accessToken' => $data['botAccessToken'],
            'bot_link' => $data['botLink'],
            'search_id' => $hashedCustomId,
        ]);


        return response(compact('app'));
    }

    public function appApprove(Request $request,$id){
        $app = App::find($id);

        $user = $request->user();

        if(!$user || !$app){
            return response('No data found!',499);
        }


        //verify admin

        $app->update([
            'approved'=>true,
        ]);

        return response('',200);
    }

    public function updateAccessToken(Request $request)
    {

        $this->validate($request, [
            'id' => 'required',
            'accessToken' => 'required',
        ]);


        $app = App::where('search_id', $request->id);


        if ($app) {
            $app->update(['access_token' => $request->accessToken]);

            return response('', 200);
        }

        return response('error', 500);
    }


    public function updateTelegramAccessTokenAndUsername(Request $request)
    {

        $this->validate($request, [
            'id' => 'required',
            'accessToken' => 'sometimes',
            'username' => 'sometimes',
        ]);


        $app = App::where('search_id', $request->id);


        if ($app) {

            if ($request->accessToken) {
                $app->update(['telegram_bot_access_token' => $request->accessToken]);
            }

            if ($request->username) {
                $app->update(['telegram_bot_username' => $request->username]);
            }

            return response('', 200);
        }

        return response('error', 500);
    }



    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response('Not found', 499);
        }

        if (!$id) {
            return response('Not found', 499);
        }


        $app = App::where('search_id', $id)->first();

        if (!$app) {
            return response('App Not Found ', 499);
        }

        return response(compact('app'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'active' => 'required',
        ]);


        $isActive = false;
        if ($request->active) {
            $isActive = true;
        }


        $app = App::where('search_id', $id)->first();

        if (!$app) {
            return response('Not found!', 499);
        }


        $app->update(['active' => $isActive]);

        return response('', 200);
    }
}
