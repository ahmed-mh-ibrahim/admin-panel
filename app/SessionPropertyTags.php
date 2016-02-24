<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionPropertyTags extends Model
{
	public $timestamps = false;

    protected $fillable = [
        'tag_id', "property_id"
    ];

    /* Table name */
    protected $table = 'property_tags_session';

    /**
     * Get property.
     */
    public function property()
    {
        return $this->belongsTo('App\SessionProperty');
    }

}
