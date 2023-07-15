<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class TelegramApisController extends Controller
{
    public function getChannels($app_id){

        $app =App::find($app_id);

        if(!$app){
            return response('no data found',499);
        }

        $accessToken = $app->telegram_bot_access_token;


        if(!$accessToken){
            return response('server error',599);
        }


        $api = new Api($accessToken);

        $updates = $api->getUpdates();

        if(count($updates)===0){
            return response(["message","Please add at least 1 chat message in your group or message"],299);
        }


        return response(compact('updates'));

    }

    public function setChatId(Request $request,$app_id){

        $this->validate($request,[
            'appId'=>'required',
            'chatId'=>'required',
        ]);

        $app = App::find($request->appId);

        if(!$app){
            return response('data not found',499);
        }

        $chatId = $request->chatId;

        $app->update([
            'telegram_chat_id'=>$chatId,
            'activated'=>true,
        ]);

        return response('',200);
    }
}
