<?php

namespace App\Actions;

use App\Models\App;
use App\Models\User;

class AddSchedulerAction
{
    public function execute($data, User $user,App $app)
    {
        $appId = $data['appId']; //search id
        $messages = $data['messageContent']; //array
        $url = $data['url'];
        $schedule = $data['schedule'];
        $imageScheduler = $data['imageScheduler'];
        $publishPost = $data['publishPost'];


        if (!$appId) {
            return response('App Not Found!', 499);
        }


        $nextTimeToPost =  $this->nextTimeToPost($schedule);


        $schedule = $user->schedules()->create([
            'app_id' => $app->id, //bot id
            'messageContent' => $messages,
            'url' => $url,
            'schedule' => $schedule,
            'imageScheduler' => $imageScheduler,
            'publishPost' => $publishPost,
            'next_to_post' => $nextTimeToPost,
        ]);


        return $schedule;
    }


    public function nextTimeToPost($schedule)
    {
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

        return $nextTimeToPost;
    }
}
