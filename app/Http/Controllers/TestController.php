<?php

namespace App\Http\Controllers;

use App\Actions\PostOnFacebookAction;
use App\Models\App;
use App\Models\Fail;
use App\Models\Schedule;
use Carbon\Carbon;
use DateTime;
use Exception;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index()
    {



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

            dd(json_decode($response,true));

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



        $this->createAdd($account_id,$account->id);
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
