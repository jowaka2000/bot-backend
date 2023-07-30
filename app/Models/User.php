<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function apps()
    {
        return $this->hasMany(App::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function admin(User $user)
    {
        $isAdmin = false;

        if (in_array($user->email, [
            'kimemiajohn45m@gmail.com',
        ])) {
            $isAdmin = true;
        }

        return $isAdmin;
    }

    public function role(){
        return $this->hasOne(Role::class);
    }

    public function setting(){
        return $this->hasOne(Setting::class);
    }
}
