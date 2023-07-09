<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PostOnFacebookJob;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class PostSchedulersController extends Controller
{

    public function index(Request $request){

        $user = $request->user();

        /** @var User $user */

        if(!$user){
            return response('data not found',409);
        }

        $schedulers = Schedule::schedulers($user->id);

        return response(compact('schedulers'));
    }


    public function store(Request $request)
    {


        $user = $request->user();

        /** @var User $user */

        if (!$user) {
            return response('User not found', 499);
        }


        if ($request->has('payLoad')) {

            $data = json_decode($request->get('payLoad'), true);
            $appId = $data['pageId']['id'];
            $messages = $data['messageContent']; //array
            $url = $data['url'];
            $schedule = $data['schedule'];
            $imageScheduler = $data['imageScheduler'];
            $publishPost = $data['publishPost'];


            if (!$appId) {
                return response('App Not Found!', 499);
            }


            $schedule = $user->schedules()->create([
                'app_id' => $appId,
                'messageContent' => json_encode($messages),
                'url' => $url,
                'schedule' => $schedule,
                'imageScheduler' => $imageScheduler,
                'publishPost' => $publishPost,
            ]);




            if ($request->hasFile('images')) {

                $filesNames = [];

                foreach ($request->file('images') as $image) {

                    $originalName = $image->getClientOriginalName();

                    $fileName = Str::uuid()->toString() . '_' . $originalName;

                    $image->move(public_path('/images'), $fileName);

                    array_push($filesNames, $fileName);
                }


                $schedule->update(['images' => json_encode($filesNames)]);
            }



            if($schedule->publishPost || $schedule->schedule==='once'){

                $data = [
                    'schedule'=>$schedule
                ];

                PostOnFacebookJob::dispatch($data);
            }

            return response(compact('schedule'));
        }


        return response('An error has occurred!', 500);
    }
}
