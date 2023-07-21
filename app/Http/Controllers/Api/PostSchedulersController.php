<?php

namespace App\Http\Controllers\Api;

use App\Actions\AddSchedulerAction;
use App\Actions\PostOnFacebookAction;
use App\Actions\PostOnTelegramAction;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class PostSchedulersController extends Controller
{

    public function index(Request $request, $app_id)
    {

        $user = $request->user();

        $app = App::where('search_id', $app_id)->first();

        /** @var User $user */

        if (!$user || !$app) {
            return response('data not found', 200);
        }


        $schedulers = Schedule::schedulers($app->id);

        return response(compact('schedulers'));
    }


    public function store(Request $request, PostOnFacebookAction $postOnFacebookAction, PostOnTelegramAction $postOnTelegramAction,AddSchedulerAction $addSchedulerAction)
    {

        $user = $request->user();

        /** @var User $user */

        if (!$user) {
            return response('User not found', 499);
        }



        if ($request->has('payLoad')) {

            $data = json_decode($request->get('payLoad'), true);

            $app = App::where('search_id', $data['appId'])->first();//fetch app

            $schedule = $addSchedulerAction->execute($data,$user,$app);

            /** @var Schedule $schedule */

            if ($request->hasFile('images')) {

                $filesNames = [];

                foreach ($request->file('images') as $image) {

                    $name = $image->hashName();

                    $fileName = Str::uuid()->toString() . '_' . $name;

                    $image->move(getcwd() . '/images', $fileName);

                    array_push($filesNames, $fileName);
                }


                $schedule->update(['images' => $filesNames]);
            }



            if (($schedule->publishPost || $schedule->schedule === 'once') && $app->bot_type === 'facebook-page') {
                $postOnFacebookAction->execute($schedule);
            }

            if (($schedule->publishPost || $schedule->schedule === 'once') && ($app->bot_type === 'telegram-channel' || $app->bot_type === 'telegram-group')) {
                //send message to telegram
                $postOnTelegramAction->execute($schedule);
            }


            return response(compact('schedule'));
        }


        return response('An error has occurred!', 500);
    }

    public function show($id)
    {
        $schedule = Schedule::find($id);


        if (!$schedule) {
            return response('not found', 499);
        }

        return response(compact('schedule'));
    }


    //edit

    public function addMessage(Request $request, $id)
    {
        $schedule = Schedule::find($id);


        $this->authorize('update', $schedule);

        $this->validate($request, [
            'message' => 'required',
        ]);



        if (!$schedule) {
            return response('No data found', 499);
        }

        $messages = $schedule->messageContent;

        array_push($messages, $request->message);

        $schedule->update(['messageContent' => $messages]);

        return response('', 200);
    }

    public function deleteMessage(Request $request, $id)
    {
        $schedule = Schedule::find($id);

        $this->authorize('delete', $schedule);

        $this->validate($request, [
            'messages' => 'sometimes',
        ]);


        if (!$schedule) {
            return response('No data found', 499);
        }

        $schedule->update(['messageContent' => $request->messages]);

        return response('', 200);
    }

    public function deleteImage(Request $request, $id)
    {
        $schedule = Schedule::find($id);

        $this->authorize('delete', $schedule);

        $this->validate($request, [
            'images' => 'sometimes',
            'imageDeleted' => 'required',
        ]);


        if (!$schedule) {
            return response('No data found', 499);
        }

        //delete image
        if (File::exists(asset('images/') . $request->imageDeleted)) {
            File::delete(asset('images/') . $request->imageDeleted);
        }

        $schedule->update(['images' => $request->images]);

        $schedule = Schedule::find($id);

        return response(compact('schedule'));
    }

    public function updateImage(Request $request, $id)
    {

        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response('No data found', 499);
        }

        if ($request->hasFile('images')) {

            $images = $schedule->images;

            if ($images && count($images) > 0) {

                foreach ($request->file('images') as $image) {

                    $name = $image->hashName();

                    $fileName = Str::uuid()->toString() . '_' . $name;

                    $image->move(getcwd() . '/images', $fileName);

                    array_push($images, $fileName);
                }
            } else {
                $images = [];

                foreach ($request->file('images') as $image) {

                    $name = $image->hashName();

                    $fileName = Str::uuid()->toString() . '_' . $name;

                    $image->move(getcwd() . '/images', $fileName);

                    array_push($images, $fileName);
                }
            }


            $schedule->update(['images' => $images]);

            $schedule = Schedule::find($id);

            return response(compact('schedule'));
        } else {
            return response('No data found', 499);
        }
    }

    public function updateUrl(Request $request, $id)
    {

        $schedule = Schedule::find($id);

        $this->validate($request, [
            'url' => 'required',
        ]);


        if (!$schedule) {
            return response('No data found', 499);
        }

        $schedule->update(['url' => $request->url]);
        $schedule = Schedule::find($id);

        return response(compact('schedule'));
    }

    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::find($id);

        $this->validate($request, [
            'schedule' => 'required',
        ]);


        if (!$schedule) {
            return response('No data found', 499);
        }


        $schedule->update(['schedule' => $request->schedule]);

        $schedule = Schedule::find($id);

        return response(compact('schedule'));
    }

    public function deleteUrl(Request $request, $id)
    {
        $schedule = Schedule::find($id);


        if (!$schedule) {
            return response('No data found', 499);
        }


        $schedule->update(['url' => null]);

        $schedule = Schedule::find($id);

        return response(compact('schedule'));
    }
    public function destroy($id)
    {

        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response('Not found', 499);
        }

        // $images = json_decode($schedule->images, true);

        // if (count($images) > 0) {
        //     foreach ($images as $image) {
        //         if (File::exists(asset('images/') . $image)) {
        //             File::delete(asset('images/') . $image);
        //         }
        //     }
        // }


        $schedule->delete();

        return response('', 200);
    }
}
