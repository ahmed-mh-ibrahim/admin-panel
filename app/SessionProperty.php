<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionProperty extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name',
        'property_type',
        'property_condition',
        'property_description',
        'address',
        'city',
        'province',
        'land_size',
        'building_size',
        'postcode',
        'country',
        'latitude',
        'longitude',
        'selling_price',
        'bedrooms',
        'bathrooms',
        'parkings',
        'certificate',
        'orientation',
        'furnish_condition',
        'status',
        'users_id'
    ];

    /* Table name */
    protected $table = 'property_session';

    /**
     * Get the images
     */
    public function images()
    {
    	//assign foreign key name since session images&tags tables don't follow tablename_id convention
        return $this->hasMany('App\SessionPropertyImages',"property_id");
    }

    /**
     * Get the tags
     */
    public function tags()
    {
        return $this->hasMany('App\SessionPropertyTags',"property_id");
    }
}
