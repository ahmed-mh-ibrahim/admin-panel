<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropertyImages extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'src', "property_id","cover"
    ];

    /* Table name */
    protected $table = 'property_images';

    /**
     * Get property.
     */
    public function property()
    {
        return $this->belongsTo('App\Property');
    }
}
