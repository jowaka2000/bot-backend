<?php

use App\Http\Controllers\TestController;
use App\Models\App;
use App\Models\Schedule;
use App\Models\User;
// use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\AdSetFields;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Str;
use Telegram\Bot\Api;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {


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

    dd($schedulers);


        if(count($schedulers)>0){

            foreach($schedulers as $scheduler){
                $app= $scheduler->app;
                dd($scheduler);


            }
        }

    $user = User::find(1);






    $api  = new Api();



    // $response =  Telegram::getMe();
    $updates = $api->getUpdates();

    $data = json_decode($updates[2], true);

    $re = Telegram::sendMessage([
        'chat_id' => $data['message']['chat']['id'],
        'text' => 'Hachiuw!'
    ]);



    dd($re);


    // $response = Telegram::sendMessage([
    //     'chat_id' => $data['message']['chat']['id'],
    //     'text' => 'I am from california'
    // ]);


    // dd($response);

    // foreach($updates as $key=>$update){
    //     $data = json_decode($update,true);


    //     if(array_key_exists('channel_post',$data)){
    //         //telegram channel
    //         dd($data);
    //     }

    //     if(array_key_exists('message',$data)){
    //         //bot message
    //         dd($data);
    //     }

    //     if(array_key_exists('my_chat_member',$data)){
    //         //telegram group
    //         dd($data);
    //     }



    // }



    $id = $data['message']['chat']['id'];

    $re = Telegram::sendMessage([
        'chat_id' => $id,
        'text' => 'Hello world!'
    ]);


    dd($re);



    $response = Telegram::sendMessage([
        'chat_id' => -877521648,
        'text' => 'Hello Members'
    ]);


    dd($response);

    // //XzWjKXEA36p3
    // //post link and message

    // $app = App::find(1);
    // $pageId = $app->page_id;
    // $accessToken = '';

    // $link = '';
    // $message = "Long lived phrase gpt";
    // try {
    //     $response = Http::post("https://graph.facebook.com/{$pageId}/feed", [
    //         'message' => $message,
    //         'link' => $link,
    //         'access_token' => $accessToken,
    //     ]);


    //     dd($response);
    // } catch (Exception $error) {
    //     dd($error);
    //     // echo "Error occurred while posting: " . $error->getMessage();
    // }


    // dd('sandsadasdas');



    $longLivedMuigaiAccessToken = '';
    $expiring = 5105031;
    $generatedOn = '8 sep 2023';


    // //getting gapeLongLive access token

    // $phraseMaskLongLiveAccessToken = '';

    // try {
    //     $response = Http::get("https://graph.facebook.com/v17.0/1585553401966102/accounts", [
    //         'access_token' => $longLivedMuigaiAccessToken,
    //     ]);

    //     // Process the response as needed
    //     // ...

    //     // Example usage: Accessing response data
    //     $responseData = json_decode($response);

    //     dd($responseData);
    //     // ...

    // } catch (\Exception $error) {
    //     dd('error');
    //     // echo 'Error occurred while making the request: ' . $error->getMessage();
    // }


    // //get user long lived access token

    // try {
    //     $response = Http::get("https://graph.facebook.com/v17.0/oauth/access_token", [
    //         'grant_type' => 'fb_exchange_token',
    //         'client_id' => 642419814078413,
    //         'client_secret' => 'f5cd33c0f9ea5193d686c078a8c33016',
    //         'fb_exchange_token' => '',
    //     ]);

    //     // Handle the response
    //     $responseData = json_decode($response);

    //     dd($responseData);
    //     // Do something with the response data

    // } catch (\Exception $error) {

    //     dd('error');
    //     // echo 'Error occurred while making the request: ' . $error->getMessage();
    // }




    // // //post link and message
    // // $link = '';
    // // $message = "Are you seeking for a job";
    // // try {
    // //     $response = Http::post("https://graph.facebook.com/{$pageId}/feed", [
    // //         'message' => $message,
    // //         'link' => $link,
    // //         'access_token' => $accessToken,
    // //     ]);


    // //     dd($response);
    // // } catch (Exception $error) {
    // //     dd($error);
    // //     // echo "Error occurred while posting: " . $error->getMessage();
    // // }

    return view('welcome');
});


Route::get('/test', [TestController::class, 'index']);
