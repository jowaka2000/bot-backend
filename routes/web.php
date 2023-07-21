<?php

use App\Http\Controllers\TestController;
use App\Models\App;
use App\Models\Schedule;
use App\Models\User;
// use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\AdSetFields;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

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





    $app = App::find(1);


    $accessToken = $app->telegram_bot_access_token;
    $chat_id = $app->telegram_chat_id;

    $api = new Api($accessToken);
    $imageUlr = 'https://phrasemaskgpt.space/static/media/advert.059f9f8c60157d5e41f3.jpg';


    $re = InputFile::create($imageUlr, 'Bot Upload');



    try {
        $response = $api->sendPhoto([
            'chat_id' => $chat_id,
            'photo' => $re,
            'caption' => 'Get Invited to our strong hold and every time we want'
        ]);



        dd($response);
    }catch(Exception $e){
dd($e);
    }



    $updates = $api->getUpdates();



    $chats= [];

    foreach($updates as $update){
        $newUpdate =json_decode( $update,true);

        if(array_key_exists('message',$newUpdate)){
            $id = $newUpdate['message']['chat']['id'];
            if($id===$chat_id){
                array_push($chats,$update);
            }
        }

        if(array_key_exists('channel_post',$newUpdate)){
           $id= $newUpdate['channel_post']['chat']['id'];

           if($id===$chat_id){
            array_push($chats,$update);
        }

        }
    }

    dd($chats);




    $update =json_decode( $updates[1],true);

    dd($update);
    dd(array_key_exists('message',$update));


    dd($update['message']);

    $update =json_decode( $updates[0],true);

    dd($update);



    $schedule = Schedule::find(14);


    $messages = $schedule->messageContent;


    dd($messages);


    if (count($messages) > 0) {

    }else{
        $messages='NEW MESSAGE';
    }

    dd(json_encode($messages));


    $myArray = array(['neno mja','pikipiki','harusi']);



    unset($myArray[0][1]);

    dd($myArray);





    $ACCESS_TOKEN='1534900936124125186-0FMfGSmWWPayg5dpo1ySnp8tpUYq8k';

    $USERNAME = '@buitengebieden';

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ACCESS_TOKEN,
        ])->get("https://api.twitter.com/2/users/by/username/@Kimemia08845017");

        dd($response);
        if ($response->successful()) {
            $data = $response->json();
            // Process the response data


        } else {
            // Handle unsuccessful response
            $statusCode = $response->status();
            // Handle specific error scenarios


        }
    } catch (\Exception $e) {
        // Handle any exceptions that occur during the request
    }




    // dd('sandsadasdas');

    // $schedulers = Schedule::join('apps', 'apps.id', '=', 'schedules.app_id')
    // ->where('schedules.active', true)
    // ->where('apps.active', true)
    // ->where('apps.activated', true)
    // ->where('apps.approved', true)
    // ->where(function ($query) {
    //     return $query->where('apps.bot_type', '=', 'telegram-channel')->orWhere('apps.bot_type', '=', 'telegram-group');
    // })
    // ->whereNot('schedules.schedule','=','once')
    // ->select('schedules.*')
    // ->get();

    // dd($schedulers);


    // if(count($schedulers)>0){

    //     foreach($schedulers as $scheduler){
    //         $app= $scheduler->app;
    //         dd($scheduler);


    //     }
    // }

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
