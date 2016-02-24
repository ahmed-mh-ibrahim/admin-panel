<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Users\Repository as UserRepository;
use App\Country;
use App\City;
use App\Province;
use App\Tag;

class GlobalDataComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    /*public function __construct(UserRepository $users)
    {
        // Dependencies automatically resolved by service container...
        $this->users = $users;
    }*/

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        //All Countries
        $view->with('countries', Country::all());

        //All Cities
        $view->with('cities', City::all());

        //All Provinces
        $view->with('provinces', Province::all());

        //All Tags
        $tags = Tag::all();
        $view->with('tags', $tags);

        //Tags map
        $tagsMap = array();
        foreach($tags as $tag)
        {
            $newtag = [ "$tag->id" => "$tag->name"];
            $tagsMap = array_merge($tagsMap, $newtag);
        }

        $view->with('tagsMap', $tagsMap);

        //All property type
        $propertyTypes = ["house","apartment"];
        $view->with('propertyTypes', $propertyTypes);

        //All property Condition
        $propertyConditions = [ "new", "used", "newly renovated"];
        $view->with('propertyConditions', $propertyConditions);

        //All certificates
        $certificates = [ "Hak Milik", "Strata Title"];
        $view->with('certificates', $certificates);

        //All orientations
        $orientations = [ "North", "Northeast", "East", "South east", "South", "South West", "West", "Northwest"];
        $view->with('orientations', $orientations);

        //All Furnish Conditions
        $furnishConditions = [ "unfurnished", "semi furnished", "fully furnished"];
        $view->with('furnishConditions', $furnishConditions);

    }
}

?>