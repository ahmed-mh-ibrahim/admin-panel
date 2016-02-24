<?php
use App\Province;
use App\City;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

//View::addExtension('html', 'blade.php');
View::addLocation(public_path());

Route::group(['middleware' => ['web']], function () {
    //Add web middleware routes here
});



//retrieving data from backend to front end
Route::get('/adduserformdata', 'CommController@getAddUserFormData');

Route::group(['middleware' => 'web'], function () {
    Route::auth(); 

    Route::post("/islogin", function(){
    if(Auth::check())
    {
        return response()->json(['logged' => true]);
    }
    else
        return response()->json(['logged' => false]);
    });


    //!!must be in web middleware to for Auth::check() to return true
    Route::get("/getLoggedUser", function(){

        if(Auth::check())
        {
            return response()->json(Auth::user());
        }
    });

    //above line prints the following from ( vendor\framework\illumaniti\routning\router.php) also check illumanit foundation authenticatesUSer for implementations
    /*
		$this->get('login', 'Auth\AuthController@showLoginForm');
        $this->post('login', 'Auth\AuthController@login');
        $this->get('logout', 'Auth\AuthController@logout');

        // Registration Routes...
        $this->get('register', 'Auth\AuthController@showRegistrationForm');
        $this->post('register', 'Auth\AuthController@register');

        // Password Reset Routes...
        $this->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
        $this->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
        $this->post('password/reset', 'Auth\PasswordController@reset');
    */



    Route::get('/home', 'HomeController@index');

    Route::get('/', [ "middleware" => "auth", function () {  //this middle ware is added in HOmeController to make sure user is autherized to access it ( remove from here)
	    return redirect('index.html#/app/dashboard');
	}]);
});


// master admin only pages
Route::group(['middleware' => ['web','auth','admin']], function () {
    
    //dashboard data
    Route::get("dashboard","CommController@getDashboardData");

    /*User management section*/

    Route::resource('user','UserController');
    Route::post("user/{user}","UserController@updateWithFile");

    Route::get("/adduser", function(){

        //note: some data are passed to view through composer: GlobalDatacomposer
        return view("adduser");
    });

    Route::get("/addagency", function(){

        return view("addagency");
    });

    /* property management */
    Route::resource('property','PropertyController');

    Route::get("/addproperty", function(){

        //note: some data are passed to view through composer: GlobalDatacomposer
        return view("addproperty");
    });

    Route::get("propertyDecline/{property}","PropertyController@declineProperty");

    Route::get("propertyAccept/{property}","PropertyController@acceptProperty");

    Route::post("property/{property}","PropertyController@updateWithFile");

    Route::get("/addproperty-2/{propertySession}", function($propertySession){
        return view("addproperty-2", ["propertySession",$propertySession]);
    });

    Route::get("/addproperty-3", function(){
        return view("addproperty-3");
    });

    Route::get("/addproperty-4", function(){
        return view("addproperty-4");
    });

    Route::post("/property_step1", 'PropertyController@formStep1');
    Route::post("/property_step2", 'PropertyController@formStep2');
    Route::post("/property_step3", 'PropertyController@formStep3');
    Route::post("/property_step4", 'PropertyController@store');

    Route::get("/property_step1", function(){return view("addproperty-2");});
    Route::get("/property_step2", function(){return view("addproperty-3");});
    Route::get("/property_step3", function(){return view("addproperty-4");});
    Route::get("/property_step4", function(){return redirect("addproperty");});

    //image delete
    Route::get("/deleteImage/{id}", "PropertyController@destroyImage");
});
