<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'checkpoint',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Provide the list of all active border checkpoints
     *
     * @return array
     */
    public static function checkPointList()
    {
        $response = Http::withHeaders([
                    'X-API-KEY' => env('COVID19_DSP_API_KEY')
                ])->get(env('COVID19_DSP_API') . 'border/checkpoint', [
                    'status' => 'active'
                ])->json();

        return $response['data'];
    }
}
