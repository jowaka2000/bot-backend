<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeAllApps($query,$user_Id){
        return $query->where('user_id',$user_Id)->orderby('created_at','desc')->get();
    }

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }

    protected $casts = [
        'page_id'=>'int',
    ];
}
