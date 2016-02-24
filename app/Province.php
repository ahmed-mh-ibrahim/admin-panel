<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
	protected $fillable = [
        'name', "country_id"
    ];

    /* Table name */
    protected $table = 'province';

    /**
     * Get the city
     */
    public function city()
    {
        return $this->hasMany('App\City');
    }

    /**
     * Get country.
     */
    public function country()
    {
        return $this->belongsTo('App\Country');
    }
}
