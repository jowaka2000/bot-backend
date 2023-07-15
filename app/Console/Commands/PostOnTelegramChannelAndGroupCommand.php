<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class PostOnTelegramChannelAndGroupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Posting on telegram groups and channels';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $schedulers = Schedule::join('apps', 'apps.id', '=', 'schedules.app_id')
        ->where('schedules.active', true)
        ->where('apps.active', true)
        ->where('apps.activated', true)
        ->where('apps.approved', true)
        ->where(function ($query) {
            return $query->where('apps.bot_type', '=', 'telegram-channel')->orWhere('apps.bot_type', '=', 'telegram-group');
        })
        ->whereNot('schedules.schedule','=','once')
        ->select('schedules.*')
        ->get();




        if(count($schedulers)>0){

            foreach($schedulers as $scheduler){
                $app= $scheduler->app;

                $accessToken= $app->telegram_bot_access_token;
                $chatId = $app->telegram_chat_id;

                if($accessToken && $chatId){

                    $timeToPost = Carbon::parse($scheduler->next_to_post);


                    //check if time to post is past
                    if(true){
                        //we can post

                        $api = new Api($accessToken);

                        $index = $this->messageContentIndexToPost($scheduler->history,$scheduler->messageContent);


                        $message = $scheduler->messageContent;
                        $message = $message[$index];

                    try{
                        $re = $api->sendMessage([
                            'chat_id' => $chatId,
                            'text' => $message,
                        ]);

                        $history = [
                            'last message index'=>$index,
                        ];
                        $scheduler->update(['history'=>json_encode($history)]);

                    }catch(Exception $e){
                    //handle error
                    }


                    }else{
                        return false;
                    }

                }else{
                    return false;
                }
            }
        }else{
            return false;
        }

    }


    public function messageContentIndexToPost($history, $messages)
    {


        $numOfMessages = count($messages);

        if ($numOfMessages > 1) {


            $history = json_decode($history, true);


            if ($history && array_key_exists('last message index',$history)) {


                $index = $history['last message index'];


                $index = $index + 1;

                if ($index >= $numOfMessages) {
                    return 0;
                } else {
                    return $index;
                }
            }else{

                return 0;
            }
        } else {
            return 0;
        }
    }

}
