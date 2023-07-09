<?php

use App\Models\App;
use App\Models\Schedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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

Route::get('/media-bot-v001', function () {



    // //XzWjKXEA36p3
    // //post link and message

    // $app = App::find(1);
    // $pageId = $app->page_id;
    // $accessToken = 'EAAJIRwTQl80BACMkgVGxxiWHnxYjZBMgQwR3MK7v3ZCmgL5xZAgDVIXksA8VywhVPrbi8ZCLO2aw6iO4eP80qdpsq8qZCQX18SswIcKkD2lKKbQjVHKyLxF3t7IsOlRglobqshlhrzsdN8sj8n9tmkzaCkTSO5YgvUXh3QZBZAoSow7JdB5kCMw';

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



    $longLivedMuigaiAccessToken = 'EAAJIRwTQl80BAGsjsWoxzZCdI06vlbQpnBnGO0gLULmqAXOUZAUVurOBFAKS8G4oxrwzCj1m2t3bWmCfwVlyNzfjS7MB5GUR9ZB0do7WYiAVYIC6SDA3OGtklWnq05jsaP9QblpNW4IytSYe1Jwi5GMpj7wnB0O28tZBTa9a1xzKkZB0MhJUW';
    $expiring = 5105031;
    $generatedOn = '8 sep 2023';


    // //getting gapeLongLive access token

    // $phraseMaskLongLiveAccessToken = 'EAAJIRwTQl80BACMkgVGxxiWHnxYjZBMgQwR3MK7v3ZCmgL5xZAgDVIXksA8VywhVPrbi8ZCLO2aw6iO4eP80qdpsq8qZCQX18SswIcKkD2lKKbQjVHKyLxF3t7IsOlRglobqshlhrzsdN8sj8n9tmkzaCkTSO5YgvUXh3QZBZAoSow7JdB5kCMw';

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
    //         'fb_exchange_token' => 'EAAJIRwTQl80BADgpCaoA3zmAuIuYtTLX7OiybQ5oZCwj1JvDFfKGqvbWzQHoRZBfxYuZBPLsGSl0UHDAG1o7JY27avDBJ1cA4JxcD4BUz2V5Gs4pLyDmXyRzPBVBZCfZBTRuxPciCoEGvxgCDmMf06CY9vOJWlOH28YUxee6XOCeK4OudPxaHR4yZBYMQ2XP8kwZChjzZBhp4e3oliZCK1BG2CpDnf95VSLyeHjlguc1ZAxI94xHWKhwY1',
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
