<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
     use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'address_1',
        'address_2',
        'city',
        'country',
        'province',
        'postcode',
        'phone_number',
        'mobile_number',
        'avater_image',
        'company_address',
        'company_logo',
        'company_website',
        'email',
        'password',
        'company_name',
        'user_type',
        'property_post_qty',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $primaryKey = 'id';

    
     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get province.
     */
    public function userType()
    {
        return $this->belongsTo('App\UserType',"id");
    }

    /**
     * Get properties.
     */
    public function properties()
    {
        return $this->hasMany('App\Property');
    }
}
