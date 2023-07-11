<?php

namespace App\Http\Controllers\Api;

use App\Actions\PostOnFacebookAction;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;


class PostSchedulersController extends Controller
{

    public function index(Request $request)
    {


        $user = $request->user();

        /** @var User $user */

        if (!$user) {
            return response('data not found', 409);
        }

        $schedulers = Schedule::schedulers($user->id);

        return response(compact('schedulers'));
    }


    public function store(Request $request, PostOnFacebookAction $postOnFacebookAction)
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

            $app = App::where('page_id', $appId)->first();

            $nextTimeToPost = null;

            if ($schedule === 'every_one_hour') {
                $nextTimeToPost = now()->addHour();
            }
            if ($schedule === 'every_two_hours') {
                $nextTimeToPost = now()->addHours(2);
            }
            if ($schedule === 'every_three_hours') {
                $nextTimeToPost = now()->addHours(3);
            }
            if ($schedule === 'every_four_hours') {
                $nextTimeToPost = now()->addHours(4);
            }
            if ($schedule === 'every_five_hours') {
                $nextTimeToPost = now()->addHours(5);
            }
            if ($schedule === 'every_six_hours') {
                $nextTimeToPost = now()->addHours(6);
            }
            if ($schedule === 'every_eight_hours') {
                $nextTimeToPost = now()->addHours(8);
            }
            if ($schedule === 'every_twelve_hours') {
                $nextTimeToPost = now()->addHours(12);
            }
            if ($schedule === 'every_day') {
                $nextTimeToPost = now()->addDay();
            }
            if ($schedule === 'every_week') {
                $nextTimeToPost = now()->addWeek();
            }

            $schedule = $user->schedules()->create([
                'app_id' => $appId,
                'messageContent' => json_encode($messages),
                'url' => $url,
                'schedule' => $schedule,
                'imageScheduler' => $imageScheduler,
                'publishPost' => $publishPost,
                'next_to_post' => $nextTimeToPost,
            ]);


            if ($request->hasFile('images')) {

                $filesNames = [];

                foreach ($request->file('images') as $image) {

                    $originalName = $image->getClientOriginalName();

                    $fileName = Str::uuid()->toString() . '_' . $originalName;

                    $image->move(getcwd().'/images', $fileName);

                    array_push($filesNames, $fileName);
                }


                $schedule->update(['images' => json_encode($filesNames)]);
            }



            if (($schedule->publishPost || $schedule->schedule === 'once') && $app->bot_type === 'facebook-page') {
                $postOnFacebookAction->execute($schedule);
            }

            return response(compact('schedule'));
        }


        return response('An error has occurred!', 500);
    }

    public function destroy($id)
    {

        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response('Not found', 499);
        }

        $schedule->delete();

        return response('', 200);
    }
}
