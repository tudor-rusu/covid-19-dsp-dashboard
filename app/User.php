<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Http;

class User extends Authenticatable
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'checkpoint', 'username', 'import_order_number',
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
