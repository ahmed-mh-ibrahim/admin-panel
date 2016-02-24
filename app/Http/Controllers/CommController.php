<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Province;
use App\City;
use App\Country;
use App\Tag;
use App\User;
use DB;
use App\Property;


class CommController extends Controller
{
    public function getAddUserFormData()
    {
    	//All Tags
        $tags = Tag::all();

        //Tags map
        $tagsMap = array();
        foreach($tags as $tag)
        {
            $newtag = [ "$tag->id" => "$tag->name"];
            $tagsMap = array_merge($tagsMap, $newtag);
        }


        //All property type
        $propertyTypes = ["house","apartment"];

        //All property Condition
        $propertyConditions = [ "new", "used", "newly renovated"];

        //All certificates
        $certificates = [ "Hak Milik", "Strata Title"];

        //All orientations
        $orientations = [ "North", "Northeast", "East", "South east", "South", "South West", "West", "Northwest"];

        //All Furnish Conditions
        $furnishConditions = [ "unfurnished", "semi furnished", "fully furnished"];

        //all users
        $users = User::where("is_admin",false)->get();

        $properties = Property::all();

        $propertiesData = [];
        if($properties != null)
        foreach($properties as $property)
        {
            $propertiesData[] = ["property" => $property, "owner" => $property->user, "images" => $property->images,
            "tags" => $property->tags,];
        }

    	$data = [
    		"countries" => Country::all(),
    		"cities" => City::all(),
    		"provinces" => Province::all(),
    		"tagsList" => Tag::all(),
    		"tagsMap" => $tagsMap,
    		"propertyTypes" => $propertyTypes,
    		"propertyConditions" => $propertyConditions,
    		"certificates" => $certificates,
    		"orientations" => $orientations,
    		"furnishConditions" => $furnishConditions,
        "users" => $users,
        "properties"=>$propertiesData,
    	];

    	return response()->json($data);
    }

    public function getDashboardData()
    {
        $usersThisMonth = DB::Select("SELECT count(*) As value, month(created_at) AS OrderMonth FROM users WHERE month(created_at) = MONTH(NOW()) group by OrderMonth ");
        $monthlyGrowthOfNewUsers = DB::select("SELECT month(created_at) AS OrderMonth, count(*) As value FROM users WHERE Year(created_at) = Year(CURRENT_TIMESTAMP) group by OrderMonth order by OrderMonth ");
        $montlyhGrowhOfProperties = DB::Select("SELECT month(created_at) AS OrderMonth, count(*) As value FROM property WHERE Year(created_at) = Year(CURRENT_TIMESTAMP) group by OrderMonth order by OrderMonth");
        $usersVsProperty = [
            "users" => $monthlyGrowthOfNewUsers,
            "property" => $montlyhGrowhOfProperties,
        ];
        $pendingProperties = DB::Select("SELECT * From users,property where property.status = 'pending' AND property.deleted_at IS NULL  AND property.users_id = users.id ORDER BY property.created_at");
        $usersByNumberOfApprovedListings = DB::Select("SELECT *, count FROM users ,(SELECT users_id, count FROM (SELECT users_id, count(*) As count From property where status = 'accepted' AND deleted_at IS NULL  Group By users_id order by count) AS userPerAccpeted) AS outter where users.id = outter.users_id");

        $data = [

            "success" => true,
            "totalUsers" => count(User::all()),
            "newUsersThisMonth" =>$usersThisMonth[0]->value,
            "pendingListings"=>count(Property::where("status","pending")->where("deleted_at", null)->get()),
            "averageConversionDays"=> 0,
            "usersVsProperty" =>$usersVsProperty,
            "monthlyGrowthOfNewUsers" =>$monthlyGrowthOfNewUsers,
            "pendingProperties" => $pendingProperties,
            "usersByNumberOfApprovedListings" =>$usersByNumberOfApprovedListings,
        ];


        return response()->json($data);
    }
}
