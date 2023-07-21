<?php

namespace App\Http\Controllers;

use App\Actions\PostOnFacebookAction;
use App\Models\App;
use App\Models\Fail;
use App\Models\Schedule;
use Carbon\Carbon;
use DateTime;
use Exception;
// use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

class TestController extends Controller
{

    public function index()
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
                    if (true) {
                        //we can post

                        $api = new Api($accessToken);

                        if ($scheduler->images && count($scheduler->images) > 0) {
                            //image and text


                            if ($scheduler->messageContent && count($scheduler->messageContent) > 0) {

                                $index = $this->messageContentIndexToPost($scheduler->history, $scheduler->messageContent);
                                $imageIndex = $this->imageIndexToPost($scheduler->history, $scheduler->images);

                                $message = $scheduler->messageContent;
                                $message = $message[$index] . "  " . $scheduler->url;


                                $images = $scheduler->images;
                                $history = [
                                    'last message index' => $index,
                                    'last image index' => $imageIndex,
                                ];

                                $imageUlr = 'https://phrasemaskgpt.space/static/media/advert.059f9f8c60157d5e41f3.jpg';



                                //...............wer are here

                                //send Image
                                $this->sendImageContent($api, $chatId, $imageUlr);

                                //send message
                                $this->sendMessageContent($api, $chatId, $message, $history, $scheduler);
                            } else {
                                //image and url

                                dd('some images and url');

                                if ($scheduler->url) {
                                    //image and url

                                    $imageIndex = $this->imageIndexToPost($scheduler->history, $scheduler->images);

                                    $message = $scheduler->messageContent;
                                    $message = $scheduler->url;


                                    $images = $scheduler->images;

                                    $history = [
                                        'No messages' => true,
                                        'last image index' => $imageIndex,
                                    ];

                                    $imageUlr = $images[$imageIndex];

                                    //send Image
                                    $this->sendImageContent($api, $chatId, $imageUlr);


                                    //send message
                                    $this->sendMessageContent($api, $chatId, $message, $history, $scheduler);
                                } else {
                                    //only image
                                    $imageIndex = $this->imageIndexToPost($scheduler->history, $scheduler->images);

                                    $images = $scheduler->images;
                                    $imageUlr = $images[$imageIndex];
                                    $this->sendImageContent($api, $chatId, $imageUlr);
                                }
                            }
                        } else {
                            //text only
                            dd('No images');

                            if (count($scheduler->messageContent) > 0) {
                                //post message only
                                $index = $this->messageContentIndexToPost($scheduler->history, $scheduler->messageContent);
                                $message = $scheduler->messageContent;
                                $message = $message[$index] . "  " . $scheduler->url;

                                $history = [
                                    'last message index' => $index,
                                ];


                                $this->sendMessageContent($api, $chatId, $message, $history, $scheduler);
                            } else {
                                //post link only

                                $message = $scheduler->url;

                                $history = [
                                    'No messages' => true
                                ];

                                $this->sendMessageContent($api, $chatId, $message, $history, $scheduler);
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
        try {
            $re = $api->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
            ]);


            $scheduler->update(['history' => json_encode($history)]);

            dd($re);
        } catch (Exception $e) {
            //handle error
        }
    }


    public function sendImageContent($api,$chatId,$imageUrl){

        try {
            $response = $api->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $imageUrl,
                'caption' => 'Telegram Image'
            ]);



            dd($response);
        }catch(Exception $e){

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

















    //

    public function index1()
    {

        $schedule = Schedule::find(2);

        $images = json_decode($schedule->images);

        $imageIndexToPost = $this->imageIndexToPost($schedule->history, $images);

        $images = json_decode($schedule->images);
        $messages = $schedule->messageContent;


        if (count($images) > 0) {

            foreach ($images as $image) {
                dd(getcwd() . 'images/' . $image);

                if (File::exists(getcwd() . 'images/' . $image)) {

                    File::delete(getcwd() . 'images/' . $image);
                }
            }
        }

        dd('done');



        $messageIndexToPost = $this->messageContentIndexToPost($schedule->history, $messages);

        $firstImage = $images[0];



        $myUrl = 'http://api.alphabailwake.com/images/' . $firstImage;

        dd($myUrl);
        // $url = asset('/images/' . $firstImage);
        $message = $messages[$messageIndexToPost] . ' ' . $schedule->url;

        $history = [
            'last image index posted' => $imageIndexToPost,
            'last image link posted' => $myUrl,
            'last message index' => $messageIndexToPost
        ];


        $pageId = 102903699501586;
        $link = '';
        $accessToken = 'EAAJIRwTQl80BAAjeq9e5TOX5k2C83UZCXYaOw3EIxEbKJzXilQBKFynuN56unjwg2I38tqZBIh2z70zoiJshGzl6PHEv5M9FXjQOode2jbtfppF5c1ruvzuZBd4QA8ZCCh5DX5ZCg3ZA7xFyuucyR9OGEpBTn95uSIODQRTDOBoHxZA5ZCRZArU7l';






        try {
            $response = Http::post("https://graph.facebook.com/{$pageId}/photos", [
                'url' => $myUrl,
                'message' => $message,
                'link' => $link,
                'access_token' => $accessToken,
            ]);


            $frequency = $schedule->frequency;

            // $schedule->update([
            //     'history' => json_encode($history),
            //     'frequency' => $frequency + 1,
            //     'last_posted' => now(),
            //     'next_to_post' => $nextTimeToPost,

            // ]);

            $re = json_decode($response, true);

            dd($re);

            if (array_key_exists('error', $re)) {
                Fail::create([
                    'app_id' => $schedule->app_id,
                    'message' => $re['error']['message'],
                    'code' => $re['error']['code'],
                    'type' => $re['error']['code'],
                ]);
            }
        } catch (\Exception $error) {
            // dd($error);
            // echo 'Error occurred while posting: ' . $error->getMessage();
        }









        $response = Http::withHeaders([
            'api-token' => '',
            'Content-Type' => 'application/json',
        ])->post('https://stealthgpt.ai/api/stealthify', [
            'prompt' => 'Hi all people',
            'rephrase' => true,
        ]);



        $data = $response->json();


        dd($data);

        try {
            $response = Http::get('https://graph.facebook.com/pages/search', [
                'q' => 'kuma',
                'fields' => 'id,name,location,link',
                'access_token' => '',
            ]);

            dd(json_decode($response, true));

            if ($response->successful()) {
                $responseData = $response->json();
                // Process the response data as needed

                dd($responseData);
            } else {
                // Handle the unsuccessful response
                $statusCode = $response->status();
                dd($response);
                // Handle the error based on the status code
            }
        } catch (Exception $e) {
            // Handle the exception
            $errorMessage = $e->getMessage();
            // Handle the error based on the exception message
        }
    }









    public function createAdd($account_id, $ad_id)
    {

        $app_id = "661344465505237";
        $app_secret = "ac24477801b3a30b40bbf904717e21d7";
        $access_token = "";
        $account_id = 111577322000377;

        Api::init($app_id, $app_secret, $access_token); // Initialize a new Session and instantiate an Api object


        $api = Api::instance(); // The Api object is now available through singleton


        // $account = new AdAccount();
        // $account->name = 'Bot from laravel';

        $fields = array(
            AdAccountFields::ID,
            AdAccountFields::NAME
        );

        $account = (new AdAccount($account_id))->getSelf($fields); //getting details of the account, account id is page id



        $this->createAdd($account_id, $account->id);
        $start_time = (new \DateTime("+1 week"))->format(DateTime::ISO8601);
        $end_time = (new \DateTime("+2 week"))->format(DateTime::ISO8601);

        $adset = new AdSet(null, $account_id);

        dd($adset);


        $adset->setData(array(
            AdSetFields::NAME => 'My Ad Set',
            AdSetFields::OPTIMIZATION_GOAL => AdSetOptimizationGoalValues::REACH,
            AdSetFields::BILLING_EVENT => AdSetBillingEventValues::IMPRESSIONS,
            AdSetFields::BID_AMOUNT => 2,
            AdSetFields::DAILY_BUDGET => 1000,
            AdSetFields::CAMPAIGN_ID => $ad_id,
            AdSetFields::TARGETING => 'computing',
            AdSetFields::START_TIME => $start_time,
            AdSetFields::END_TIME => $end_time,

        ));

        $adSet = $adset->create(array(
            AdSet::STATUS_PARAM_NAME => AdSet::STATUS_PAUSED,
        ));

        dd($adSet);
    }
}
