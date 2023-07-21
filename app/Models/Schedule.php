<?php

namespace App\Models;

use App\Casts\ImageSchedulerCast;
use App\Casts\SchedulerCreatedAtCast;
use App\Casts\SchedulerMessageContentCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = [];



    public function app(){
        return $this->belongsTo(App::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function scopeSchedulers($query,$app_id){

        return $query->where('app_id',$app_id)->orderby('created_at','desc')->get();
    }


    protected $casts = [
        'messageContent'=>SchedulerMessageContentCast::class,
        'created_at'=>SchedulerCreatedAtCast::class,
        'images'=>ImageSchedulerCast::class,
    ];
}
