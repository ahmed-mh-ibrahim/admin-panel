<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Auth;
use Request;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Log;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    //protected $redirectTo = '/home';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        //validate fields
        $validator = Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile_number' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        return $validator; 
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'],
            'city' => $data['city'],
            'province' => $data['province'],
            'country' => $data['country'],
            'postcode' => $data['postcode'],
            'phone_number' => $data['phone_number'],
            'mobile_number' => $data['mobile_number'],
            'avater_image' => $data['avater_image'],
            'email' => $data['email'],
            'is_admin' => true,
            'password' => bcrypt($data['password']),
        ]);
    }

    public function login(Request $request)
    {
        //check if valid user login
        if (Auth::attempt(['email' => $request::get("email"), 'password' => $request::get("password")], $request::get("remember"))) {
            // Authentication passed...
            //detect if admin redirect to dashboard

            //laravel converts to json automatically
            //return response()->json(Auth::user());
            return Auth::user();
        }
        else
        {
            //return redirect("login")->with("invalid","Wrong email or password");
            return response()->json(['invalid' => 'Wrong email or password']);
        }

    }

    public function showLoginForm()
    {

        return redirect("index.html#/core/login");
    }

    public function logout()
    {
        Auth::logout();
        //return view("auth/login");
        return redirect("index.html#/core/login");
    }

}
