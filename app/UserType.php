<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'name'
    ];

    /* Table name */
    protected $table = 'usertype';

    /**
     * Get the users
     */
    public function users()
    {
        return $this->hasMany('App\User',"user_type");
    }
}
