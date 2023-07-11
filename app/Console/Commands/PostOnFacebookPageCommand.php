<?php

namespace App\Console\Commands;

use App\Models\App;
use App\Models\Fail;
use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PostOnFacebookPageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:facebook-page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to post on facebook page';

    /**
     * Execute the console command.
     */
    public function handle()
    {



        $schedulers1 = Schedule::join('apps', 'apps.page_id', '=', 'schedules.app_id')
            ->where('schedules.active', true)
            ->where('apps.bot_type', '=', 'facebook-page')
            ->select('schedules.*')
            ->get();

        $schedulers = $schedulers1->unique();


        if (count($schedulers) > 0) {

            foreach ($schedulers as $schedule) {
                $app = App::where('page_id', $schedule->app_id)->first();

                if ($app) {
                    //do all the logic here


                    if ($schedule->schedule !== 'once' && $schedule->schedule !== 'none') {


                        $this->checkToPost($schedule);
                    }
                }
            }
        }else{
            return;
        }
    }


    public function checkToPost(Schedule $schedule)
    {

        $nextToPost = Carbon::parse($schedule->next_to_post);


        if ($nextToPost->isPast()) {


            //post
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


            $this->postToFacebookPage($schedule, $nextTimeToPost);
        }else{
            // dd('not pased');
            return;
        }
    }


    public function postToFacebookPage(Schedule $schedule, $nextTimeToPost)
    {

        $app = App::where('page_id', $schedule->app_id)->first();

        if ($app && $app->bot_type == 'facebook-page') {



            $accessToken = $app->access_token;
            $pageId = $app->page_id;

            $messages = $schedule->messageContent;


            if (count($messages) === 0) {


                //there is no messages

                if ($schedule->url === '') {
                    //we are only posting images .....

                    $images = json_decode($schedule->images);

                    if ($images && count($images) > 0) {

                        //post the image......................................


                        $indexToPost = $this->imageIndexToPost($schedule->history, $images);


                        $firstImage = $images[$indexToPost];

                        $url = asset('/images/' . $firstImage);

                        $history = [
                            'last image index posted' => $indexToPost,
                            'last image link posted' => $url,
                        ];

                        $this->messageAndImage($pageId, $url, '', '', $accessToken, $schedule, $history, $nextTimeToPost);

                        //...........................................................end
                    }
                } else {
                    // post url ................................................

                    $images = json_decode($schedule->images);

                    if ($images && count($images) > 0) {
                        //post the image and url

                        $indexToPost = $this->imageIndexToPost($schedule->history, $images);

                        $firstImage = $images[$indexToPost];

                        $url = asset('/images/' . $firstImage);

                        $history = [
                            'last image index posted' => $indexToPost,
                            'last image link posted' => $url,
                        ];

                        $this->messageAndImage($pageId, $url, $schedule->url, $schedule->url, $accessToken, $schedule, $history, $nextTimeToPost);
                    } else {

                        $message = '';
                        $link = $schedule->url;

                        $history = [
                            'Only link posted' => true
                        ];

                        $this->messageAndUrl($pageId, $accessToken, $message, $link, $schedule, $history, $nextTimeToPost);
                    }

                    //.........................................................end
                }
            } else {
                //there are messages


                $images = json_decode($schedule->images);


                if ($images && count($images) > 0) {
                    //post the image and the message -----------------------------------------------------
                    dd('images');
                    $imageIndexToPost = $this->imageIndexToPost($schedule->history, $images);

                    $messageIndexToPost = $this->messageContentIndexToPost($schedule->history, $messages);

                    $firstImage = $images[$imageIndexToPost];

                    $url = asset('/images/' . $firstImage);
                    $message = $messages[$messageIndexToPost] .' '. $schedule->url;

                    $history = [
                        'last image index posted' => $imageIndexToPost,
                        'last image link posted' => $url,
                        'last message index' => $messageIndexToPost
                    ];

                    $this->messageAndImage($pageId, $url, $message, $schedule->url, $accessToken, $schedule, $history, $nextTimeToPost);
                } else {
                    //0000000000000000000000000000---sorted first time
                    //....................................continue

                    $messageIndexToPost = $this->messageContentIndexToPost($schedule->history, $messages);



                    $message = $messages[$messageIndexToPost];
                    $link = $schedule->url;


                    $history = [
                        'last message index' => $messageIndexToPost
                    ];



                    $this->asset($pageId, $accessToken, $message, $link, $schedule, $history, $nextTimeToPost);

                    //...............................................end
                }
            }
        }else{
            return;
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


    public function imageIndexToPost($history, $images)
    {


        $numOfImages = count($images);

        if ($numOfImages > 1) {

            $history = json_decode($history, true);

            if ($history && array_key_exists('last image index posted',$history)) {
                $index = $history['last image index posted'];


                $index = $index + 1;

                if ($index >= $numOfImages) {
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


    public function messageAndUrl($pageId, $accessToken, $messages, $link, Schedule $schedule, $history, $nextTimeToPost)
    {

        $message = $messages;



        try {
            $response = Http::post("https://graph.facebook.com/{$pageId}/feed", [
                'message' => $message,
                'link' => $link,
                'access_token' => $accessToken,
            ]);


            $frequency = $schedule->frequency;

            $schedule->update([
                'history' => json_encode($history),
                'frequency' => $frequency + 1,
                'last_posted' => now(),
                'next_to_post' => $nextTimeToPost,
            ]);



            $re = json_decode($response, true);

            if (array_key_exists('error',$re)) {
                Fail::create([
                    'app_id' => $schedule->page_id,
                    'message' => $re['error']['message'],
                    'code' => $re['error']['code'],
                    'type' => $re['error']['code'],
                ]);
            }

            // dd($re);
        } catch (Exception $error) {

            // dd($error);
            // echo "Error occurred while posting: " . $error->getMessage();
        }
    }

    public function messageAndImage($pageId, $photoUrl, $message, $link, $accessToken, Schedule $schedule, $history, $nextTimeToPost)
    {

        try {
            $response = Http::post("https://graph.facebook.com/{$pageId}/photos", [
                'url' => $photoUrl,
                'message' => $message,
                'link' => $link,
                'access_token' => $accessToken,
            ]);


            $frequency = $schedule->frequency;

            $schedule->update([
                'history' => json_encode($history),
                'frequency' => $frequency + 1,
                'last_posted' => now(),
                'next_to_post' => $nextTimeToPost,

            ]);

            $re = json_decode($response, true);

            if (array_key_exists('error',$re)) {
                Fail::create([
                    'app_id' => $schedule->page_id,
                    'message' => $re['error']['message'],
                    'code' => $re['error']['code'],
                    'type' => $re['error']['code'],
                ]);
            }

        } catch (\Exception $error) {
            // dd($error);
            // echo 'Error occurred while posting: ' . $error->getMessage();
        }
    }
}
