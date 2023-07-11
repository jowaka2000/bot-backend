<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAppStoreRequest;
use App\Models\App;
use App\Models\User;
use Illuminate\Http\Request;

class UserAppsController extends Controller
{

    public function index(Request $request)
    {

        $user = $request->user();

        /** @var User $user */

        if (!$user) {
            return response('Your data is not found', 499);
        }


        $apps = App::allApps($user->id);

        return response(compact('apps'));
    }


    public function store(UserAppStoreRequest $request)
    {
        $data = $request->validated();


        $user = $request->user();

        /** @var User $user */

        if (!$user) {
            return response('No user found', 499);
        }

        $app = $user->apps()->create([
            'bot_type' => $data['botType'],
            'app_name' => $data['appName'],
            'page_name' => $data['pageName'],
            'page_id' => $data['pageID'],
        ]);


        return response(compact('app'));
    }


    public function updateAccessToken(Request $request)
    {

        $this->validate($request, [
            'pageId' => 'required',
            'accessToken' => 'required',
        ]);



        $app = App::where('page_id', $request->pageId)->first();


        if ($app) {
            $app->update(['access_token' => $request->accessToken]);

            return response('', 200);
        }

        return response('error', 500);
    }


    public function show(Request $request, $id)
    {


        $id = (int)$id;



        $user = $request->user();

        if (!$user) {
            return response('Not found', 499);
        }
        if (!$id) {
            return response('Not found', 499);
        }


        $app = App::where('page_id', $id)->first();

        if (!$app) {
            return response('App Not Found ', 499);
        }

        return response(compact('app'));
    }
}
