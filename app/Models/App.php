<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAllApps($query, $user_Id)
    {

        $user = User::find($user_Id);

        if ($user && $user->admin($user)) {
            $query = $query->orderby('created_at', 'desc')->get();
        } else {
            $query = $query->where('user_id', $user_Id)->orderby('created_at', 'desc')->get();
        }

        return $query;
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    protected $casts = [
        'page_id' => 'int',
        'active' => 'boolean',
        'subscribed' => 'boolean',
        'approved' => 'boolean',
        'activated' => 'boolean',
        'telegram_chat_id'=>'int',
    ];
}
