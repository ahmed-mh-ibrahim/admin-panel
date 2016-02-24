<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /* Table name */
    protected $table = 'country';

    /**
     * Get the province
     */
    public function province()
    {
        return $this->hasMany('App\Province');
    }

    
}
