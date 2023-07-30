<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
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
            //add subscription
            ->where(function ($query) {
                return $query->where('apps.bot_type', '=', 'telegram-channel')->orWhere('apps.bot_type', '=', 'telegram-group');
            })
            ->whereNot('schedules.schedule', '=', 'once')
            ->select('schedules.*')
            ->get();




        if (count($schedulers) > 0) {

            foreach ($schedulers as $scheduler) {
                $app = $scheduler->app;

                $accessToken = $app->telegram_bot_access_token;
                $chatId = $app->telegram_chat_id;


                if ($accessToken && $chatId) {

                    $timeToPost = Carbon::parse($scheduler->next_to_post);


                    //check if time to post is past
                    if ($timeToPost->isPast()) {
                        //we can post

                        $api = new Api($accessToken);


                        if ($scheduler->images && count($scheduler->images) > 0) {
                            //image and text



                            if ($scheduler->messageContent && count($scheduler->messageContent) > 0) {

                                //------------------------------------------------------------------checked

                                $index = $this->messageContentIndexToPost($scheduler->history, $scheduler->messageContent);
                                $imageIndex = $this->imageIndexToPost($scheduler->history, $scheduler->images);

                                $message = $scheduler->messageContent;
                                $caption = $message[$index] . "  " . $scheduler->url; //message $url


                                $images = $scheduler->images;

                                $imageUlr = asset('images/') . $images[$imageIndex];

                                $history = [
                                    'last message index' => $index,
                                    'last image index' => $imageIndex,
                                ];


                                //send Image and message as caption
                                $this->sendImageContent($api, $chatId, $imageUlr, 'Telegram File Name', $caption, $scheduler, $history);


                                //----------------------------------------------------------------
                            } else {
                                //image and url

                                if ($scheduler->url) {
                                    //image and url

                                    //------------------------------------checked

                                    $imageIndex = $this->imageIndexToPost($scheduler->history, $scheduler->images);

                                    $message = $scheduler->messageContent;
                                    $caption = $scheduler->url; //message/url


                                    $images = $scheduler->images;

                                    $history = [
                                        'No messages' => true,
                                        'last image index' => $imageIndex,
                                    ];

                                    $imageUlr = asset('images/') . $images[$imageIndex];

                                    //send Image
                                    $this->sendImageContent($api, $chatId, $imageUlr, 'Telegram File Name', $caption, $scheduler, $history);

                                    //------------------------------------------------------------------------------------------
                                } else {
                                    //only image

                                    //-------------------------------------------------------checked

                                    $imageIndex = $this->imageIndexToPost($scheduler->history, $scheduler->images);

                                    $images = $scheduler->images;

                                    $imageUlr = asset('images/') . $images[$imageIndex];

                                    $caption = '';



                                    $history = [
                                        'no url and messages' => true,
                                    ];

                                    $this->sendImageContent($api, $chatId, $imageUlr, 'Telegram File Name', $caption, $scheduler, $history);

                                    //-----------------------------------------------------------------
                                }
                            }
                        } else {
                            //text only

                            if ($scheduler->messageContent && count($scheduler->messageContent) > 0) {
                                //post message only

                                //---------------------------------------------------checked

                                $index = $this->messageContentIndexToPost($scheduler->history, $scheduler->messageContent);
                                $message = $scheduler->messageContent;
                                $message = $message[$index] . "  " . $scheduler->url;

                                $history = [
                                    'last message index' => $index,
                                ];


                                $this->sendMessageContent($api, $chatId, $message, $history, $scheduler);


                                //---------------------------------------------------------
                            } else {
                                //post link only
                                //----------------------------------------------checked
                                if ($scheduler->url) {

                                    $message = $scheduler->url;

                                    $history = [
                                        'No messages' => true
                                    ];

                                    $this->sendMessageContent($api, $chatId, $message, $history, $scheduler);
                                } else {
                                    return false;
                                }

                                //------------------------------------------------------------------------------------

                            }
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function sendMessageContent($api, $chatId, $message, $history, $scheduler)
    {

        $nextTimeToPost = $this->nextTimeToPost($scheduler);

        try {
            $re = $api->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
            ]);

            $frequency = $scheduler->frequency;


            $scheduler->update([
                'history' => json_encode($history),
                'frequency' => $frequency + 1,
                'last_posted' => now(),
                'next_to_post' => $nextTimeToPost,
            ]);
        } catch (Exception $e) {
            //handle error
        }
    }


    public function sendImageContent($api, $chatId, $imageUrl, $fileName, $caption, $scheduler, $history)
    {

        $nextTimeToPost = $this->nextTimeToPost($scheduler);

        $image = InputFile::create($imageUrl, $fileName);

        try {
            $response = $api->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $image,
                'caption' => $caption,
            ]);

            $frequency = $scheduler->frequency;

            $scheduler->update([
                'history' => json_encode($history),
                'frequency' => $frequency + 1,
                'last_posted' => now(),
                'next_to_post' => $nextTimeToPost,
            ]);
        } catch (Exception $e) {
        }
    }

    public function imageIndexToPost($history, $images)
    {


        $numOfImages = count($images);

        if ($numOfImages > 1) {

            $history = json_decode($history, true);

            if ($history && array_key_exists('last image index', $history)) {
                $index = $history['last image index'];


                $index = $index + 1;

                if ($index >= $numOfImages) {
                    return 0;
                } else {
                    return $index;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }



    public function messageContentIndexToPost($history, $messages)
    {


        $numOfMessages = count($messages);

        if ($numOfMessages > 1) {


            $history = json_decode($history, true);


            if ($history && array_key_exists('last message index', $history)) {


                $index = $history['last message index'];


                $index = $index + 1;

                if ($index >= $numOfMessages) {
                    return 0;
                } else {
                    return $index;
                }
            } else {

                return 0;
            }
        } else {
            return 0;
        }
    }


    public function nextTimeToPost($schedule)
    {
        $nextTimeToPost = null;

        if ($schedule->schedule === 'every_one_hour') {
            $nextTimeToPost = now()->addHour();
        }
        if ($schedule->schedule === 'every_two_hours') {
            $nextTimeToPost = now()->addHours(2);
        }
        if ($schedule->schedule === 'every_three_hours') {
            $nextTimeToPost = now()->addHours(3);
        }
        if ($schedule->schedule === 'every_four_hours') {
            $nextTimeToPost = now()->addHours(4);
        }
        if ($schedule->schedule === 'every_five_hours') {
            $nextTimeToPost = now()->addHours(5);
        }
        if ($schedule->schedule === 'every_six_hours') {
            $nextTimeToPost = now()->addHours(6);
        }
        if ($schedule->schedule === 'every_eight_hours') {
            $nextTimeToPost = now()->addHours(8);
        }
        if ($schedule->schedule === 'every_twelve_hours') {
            $nextTimeToPost = now()->addHours(12);
        }
        if ($schedule->schedule === 'every_day') {
            $nextTimeToPost = now()->addDay();
        }
        if ($schedule->schedule === 'every_week') {
            $nextTimeToPost = now()->addWeek();
        }

        return $nextTimeToPost;
    }
}
