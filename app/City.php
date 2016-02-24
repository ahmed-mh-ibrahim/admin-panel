<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name', "province_id"
    ];

    /* Table name */
    protected $table = 'city';

    /**
     * Get province.
     */
    public function province()
    {
        return $this->belongsTo('App\Province');
    }
}
