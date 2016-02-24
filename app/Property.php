<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_type',
        'name',
        'property_condition',
        'property_description',
        'address',
        'city',
        'land_size',
        'building_size',
        'province',
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
    protected $table = 'property';

    /**
     * Get the images
     */
    public function images()
    {
        return $this->hasMany('App\PropertyImages');
    }

    /**
     * Get the tags
     */
    public function tags()
    {
        return $this->hasMany('App\PropertyTags');
    }

     /**
     * Get owner
     */
    public function user()
    {
        return $this->belongsTo('App\User',"users_id");
    }
}
