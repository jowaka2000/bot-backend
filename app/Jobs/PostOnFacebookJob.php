<?php

namespace App\Jobs;

use App\Models\App;
use App\Models\Fail;
use App\Models\Schedule;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PostOnFacebookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $schedule = $this->data['schedule'];

        $app = App::where('page_id', $schedule['app_id'])->first();

        if ($app && $app->bot_type == 'facebook-page') {
            $accessToken = $app->access_token;
            $pageId = $app->page_id;

            $messages = $schedule->messageContent;
            $messages = json_decode($messages);

            if (count($messages) === 0) {
                //there is no messages

                if ($schedule->url === '') {
                    //there is no url
                    //check images

                    //we are only posting images .....

                    $images = json_decode($schedule->images);

                    if ($images && count($images) > 0) {
                        //post the image

                        $firstImage = $images[0];

                        $url = asset('/images/' . $firstImage);

                        $history = [
                            'last image index posted' => 0,
                            'last image link posted' => $url,
                        ];

                        $this->messageAndImage($pageId, $url, '', '', $accessToken, $schedule, $history);
                    }
                } else {
                    //post url
                    //check images

                    $images = json_decode($schedule->images);

                    if ($images && count($images) > 0) {
                        //post the image

                        $firstImage = $images[0];

                        $url = asset('/images/' . $firstImage);

                        $history = [
                            'last image index posted' => 0,
                            'last image link posted' => $url,
                        ];

                        $this->messageAndImage($pageId, $url, $schedule->url, $schedule->url, $accessToken, $schedule, $history);
                    }
                }
            } else {
                //there are messages

                $images = json_decode($schedule->images);

                if ($images && count($images) > 0) {
                    //post the image and the message

                    $firstImage = $images[0];
                    $url = asset('/images/' . $firstImage);
                    $message = $messages[0] . $schedule->url;

                    $history = [
                        'last image index posted' => 0,
                        'last image link posted' => $url,
                        'last message index' => 0
                    ];

                    $this->messageAndImage($pageId, $url, $message, $schedule->url, $accessToken, $schedule, $history);
                } else {
                    //post messages and link

                    $message = $messages[0];
                    $link = $schedule->url;

                    $history = [
                        'last message index' => 0
                    ];

                    $this->messageAndUrl($pageId, $accessToken, $message, $link, $schedule, $history);
                }
            }
        }
    }

    public function messageAndUrl($pageId, $accessToken, $messages, $link, Schedule $schedule, $history)
    {

        $message = $messages[0];

        try {
            $response = Http::post("https://graph.facebook.com/{$pageId}/feed", [
                'message' => $message,
                'link' => $link,
                'access_token' => $accessToken,
            ]);


            $frequency = $schedule->frequency;

            $schedule->update(['history' => json_encode($history), 'frequency' => $frequency + 1]);




            $re = json_decode($response, true);

            if ($re['error']) {
                Fail::create([
                    'app_id' => $schedule->page_id,
                    'message' => $re['error']['message'],
                    'code' => $re['error']['code'],
                    'type' => $re['error']['code'],
                ]);
            }
        } catch (Exception $error) {


            // echo "Error occurred while posting: " . $error->getMessage();
        }
    }


    public function messageAndImage($pageId, $photoUrl, $message, $link, $accessToken, Schedule $schedule, $history)
    {

        try {
            $response = Http::post("https://graph.facebook.com/{$pageId}/photos", [
                'url' => $photoUrl,
                'message' => $message,
                'link' => $link,
                'access_token' => $accessToken,
            ]);



            $frequency = $schedule->frequency;

            $schedule->update(['history' => json_encode($history), 'frequency' => $frequency + 1]);

            $re = json_decode($response, true);

            if ($re['error']) {
                Fail::create([
                    'app_id' => $schedule->page_id,
                    'message' => $re['error']['message'],
                    'code' => $re['error']['code'],
                    'type' => $re['error']['code'],
                ]);
            }
        } catch (\Exception $error) {

            // echo 'Error occurred while posting: ' . $error->getMessage();
        }
    }
}
