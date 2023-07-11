<?php

namespace App\Models;

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


    public function scopeSchedulers($query,$user_id){

        $user= User::find($user_id);

        if($user->email==='kimemiajohn@gmail.com'){
            $query = $query->orderby('created_at','desc')->get();
        }else{
            $query = $query->where('user_id',$user_id)->orderby('created_at','desc')->get();
        }

        return $query;
    }


    protected $casts = [
        'messageContent'=>SchedulerMessageContentCast::class,
        'created_at'=>SchedulerCreatedAtCast::class,
    ];
}
