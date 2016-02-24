<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
     protected $fillable = [
        'name'
    ];

    /* Table name */
    protected $table = 'tags';

    /**
     * Get the property
     */
    public function properties()
    {
        return $this->hasMany('App\Property');
    }
}
