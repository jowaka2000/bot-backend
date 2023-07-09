<?php

namespace App\Console\Commands;

use App\Models\App;
use App\Models\Schedule;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PostOnFacebookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Posting on Facebook from laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $scheduler = Schedule::where('app_id', 642419814078413)->first();

        if ($scheduler) {

            $app = App::where('page_id',$scheduler->app_id)->first();

            $accessToken = $app->access_token;

            $pageId = 102903699501586;
            $pageAccessToken = $accessToken;
            $message = 'I am testing my app from laravel';


            try {
                $response = Http::post("https://graph.facebook.com/{$pageId}/feed", [
                    'message' => $message,
                    'access_token' => $pageAccessToken,
                ]);


                dd($response);
            } catch (Exception $error) {
                echo "Error occurred while posting: " . $error->getMessage();
            }
        }
    }
}
