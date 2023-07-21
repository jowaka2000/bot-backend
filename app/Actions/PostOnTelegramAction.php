<?php

namespace App\Actions;

use App\Models\Schedule;
use Exception;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class PostOnTelegramAction
{

    public function execute(Schedule $scheduler)
    {

        $app = $scheduler->app;

        $accessToken = $app->telegram_bot_access_token;
        $chatId = $app->telegram_chat_id;


        if ($accessToken && $chatId) {

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
    }



    public function sendMessageContent($api, $chatId, $message, $history, $scheduler)
    {
        try {
            $re = $api->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
            ]);


            $scheduler->update(['history' => json_encode($history)]);
        } catch (Exception $e) {
            //handle error
        }
    }


    public function sendImageContent($api, $chatId, $imageUrl, $fileName, $caption,$scheduler,$history)
    {

        $image = InputFile::create($imageUrl, $fileName);

        try {
            $response = $api->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $image,
                'caption' => $caption,
            ]);

            $scheduler->update(['history' => json_encode($history)]);

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

}
