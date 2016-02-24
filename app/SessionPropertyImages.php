<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionPropertyImages extends Model
{
	public $timestamps = false;

    protected $fillable = [
        'src', "property_id","cover"
    ];

    /* Table name */
    protected $table = 'property_images_session';

    /**
     * Get property.
     */
    public function property()
    {
        return $this->belongsTo('App\SessionProperty');
    }
}
