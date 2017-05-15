<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'verification_token', 'verification_token_time',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_token', 'verification_token_time',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['verification_token_time'];

    /**
     * Clear user verification_token and verification_token_time
    */
    public function verify()
    {
        $this->verification_token = NULL;
        $this->verification_token_time = NULL;
        $this->is_verified = true;
        $this->save();
    }

    public function isVerified()
    {
      return $this->is_verified;
    }

}
