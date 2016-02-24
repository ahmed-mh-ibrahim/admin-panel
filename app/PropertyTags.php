<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropertyTags extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tag_id', "property_id"
    ];

    /* Table name */
    protected $table = 'property_tags';

    /**
     * Get property.
     */
    public function property()
    {
        return $this->belongsTo('App\Property');
    }

}
