<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use UploadedFile;
use App\Province;
use App\City;
use Log;


class UserController extends Controller
{
    /* File upload directory */
    protected $upload_directory = "upload/" ;

    protected function imageValidation(Request $request, $image_attribute)
    {
       //validate image  
        $image_error = false;
        $errorMessage = "";
        $file_name = null;

        if ($request->hasFile($image_attribute))
        {
            $image = $request->file($image_attribute);

            $filename  = $image->getClientOriginalExtension()."-".time();
            
            $valid_file_extensions = array("jpg", "jpeg", "gif", "png");
            $file_extension = $image->guessExtension();//strrchr($image->getClientOriginalExtension(), ".");


            if (in_array($image->guessExtension(), $valid_file_extensions) ) 
            {
                
              if ($image->getError() == UPLOAD_ERR_OK && $image->isValid())
              {
                //$imagesize = getimagesize($_FILES["file"]["tmp_name"]);
                if($image->getClientSize() < $image->getMaxFilesize())
                {
                  //create new filename
                  $randomnumber = mt_rand ();
                  $randomname =  $request->input('first_name')."-".$request->input('first_name')."-".$randomnumber;
                  $hashedrandomname = md5($randomname);
                  $hashedrandomname2 = md5($hashedrandomname);
                  $file_name = $hashedrandomname2.".".$file_extension;
                  while(file_exists($this->upload_directory . $file_name)){
                    $hashedrandomname2 = md5($file_name);
                    $file_name = $hashedrandomname2.".".$file_extension;
                  }

                  $image->move($this->upload_directory, $file_name);
                  $imageCheck = true;
                  
                }
                else{
                  $errorMessage = "Image size must be less than ".UploadFile::getMaxFilesize();
                  $image_error = true;
                }
              }
              else{
                $errorMessage = "Error in Image file: " . $image->getErrorMessage();
                $image_error = true;
              }
            }
            else
            {
                $errorMessage = "Error in Image file: file format ";
                $image_error = true;
            }
        }

        if ($image_error) {
            $validator = Validator::make([],[]);
            $validator->errors()->add($image_attribute, $errorMessage);
            $this->throwValidationException(
                $request, $validator
            );
        }

        return $file_name;
    }

     /**
     * Get a validator for an incoming user requests.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile_number' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    //email is the unique identifier since id isn't available for security
    protected function updateValidator(array $data, $email)
    {
        
        $user = User::where("email",$email)->first();

        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile_number' => 'required|max:255|unique:users,mobile_number,'.$user->id,
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = User::where("is_admin",false)->get();
        //pass users to users view
        $data = array(
                "users" => $users
            );

        //return view("users", $data);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Log::info($request);//($request, "config"));
       Log::info($request->file());
        //validate data
        $validator = $this->validator($request->all());
        //check if validation fails
        //dd($validator->errors()->getMessages());
        if ($validator->fails()) {
          $data = [
            'input' => $request,
            'errors' => $validator->errors()
          ];

          return response()->json($data);
            $this->throwValidationException(
                $request, $validator
            );
        }

        $file_name = $this->imageValidation($request,'avater_image');
        

        
        $data = $request->toArray();


        //updating profile pic location
        if($file_name != null)
            $data["avater_image"] =  $this->upload_directory.$file_name;

        //hashing password         
        $data["password"] = bcrypt($data["password"]);

        //check if agency, validate logo
        if(isset($data["user_type"]) && $data["user_type"] == 2 ) //agency
        {
          $file_name = $this->imageValidation($request,'company_logo');

          //updating logo
          if($file_name != null)
              $data["company_logo"] =  $this->upload_directory.$file_name;
        }

        //make sure $fillable or $guarded or both arrays are filled in model to user create (mass change)
        $user = User::create($data);

        //redirect to users page
        $users = User::all();
        //pass users to users view
        $data = array(
                "users" => $users
            );

        if($user != null)
        {
          $to = $user->email;
          $subject = "Welcome to Rezo! ";

          $message= "<br/>Thank you for signing up. With Rezo you have access to beautiful properties in Jakarta. Let's start ".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.html or log in to your profile here ".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.html#/core/login"; 
          $headers = 'MIME-Version: 1.0' . "\r\n";
          $headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 

          $mailsent = mail($to,$subject,$message,$headers);
          if($mailsent)
              $formSuccess = true;
          else
              Log::info("An error occurred while sending approval email");

          Log::info($message);
        }

        //return redirect("user");
        return response()->json(["success"=>true, "user" => User::find($user->id)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $data = array(
                "user" => $user
            );

        //return view("user", $data);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $user = User::find($id);
        $data = array(
                "user" => $user
            );

        //return view("edit", $data);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       Log::info($request);//($request, "config"));
       Log::info($request->file());
      return response()->json($request);//;$request->header());
        //validate data
        $user = User::find($id);
        $validator = $this->updateValidator($request->all(), $user->email);
        //check if validation fails
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        
        $data = $request->toArray();


        //update profile pic if necessary
        if(isset($data["avater_image"]) && $data["avater_image"] == "")
          $data["avater_image"] = $user["avater_image"];
        else{
          //file upload validation
          $file_name = $this->imageValidation($request,'avater_image');
          //updating profile pic location
          if($file_name != null && $file_name != "")
            $data["avater_image"] =  $this->upload_directory.$file_name;
          else
            $data["avater_image"] = $user["avater_image"];
        }

        //check if agency, validate logo
        if(isset($data["company_logo"]) && $data["company_logo"] == "")
          $data["company_logo"] = $user["company_logo"];
        else{
          if(isset($data["user_type"]) && $data["user_type"] == 2 ) //agency
          {
            $file_name = $this->imageValidation($request,'company_logo');

            //updating logo
            if($file_name != null)
                $data["company_logo"] =  $this->upload_directory.$file_name;
          }
        }

        $user->update($data);

        //return to user
        $data = array(
                "user" => $user
            );

        //return view("user", $data);
        return response()->json(["success" => true, "user" => $user]);
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWithFile(Request $request, $id)
    {
       Log::info($request);
       Log::info($request->file());
        //validate data
        $user = User::find($id);
        $validator = $this->updateValidator($request->all(), $user->email);
        //check if validation fails
        if ($validator->fails()) {
           return response()->json($data);
            $this->throwValidationException(
                $request, $validator
            );
        }
        
        $data = $request->toArray();


        //update profile pic if necessary
        if(isset($data["avater_image"]) && $data["avater_image"] == "")
          $data["avater_image"] = $user["avater_image"];
        else{
          //file upload validation
          $file_name = $this->imageValidation($request,'avater_image');
          //updating profile pic location
          if($file_name != null && $file_name != "")
            $data["avater_image"] =  $this->upload_directory.$file_name;
          else
            $data["avater_image"] = $user["avater_image"];
        }

        //check if agency, validate logo
        if(isset($data["company_logo"]) && $data["company_logo"] == "")
          $data["company_logo"] = $user["company_logo"];
        else{
          if(isset($data["user_type"]) && $data["user_type"] == "agency" )
          {
            $file_name = $this->imageValidation($request,'company_logo');

            //updating logo
            if($file_name != null)
                $data["company_logo"] =  $this->upload_directory.$file_name;
          }
        }

        $user->update($data);

        //return to user
        $data = array(
                "user" => $user
            );

        //return view("user", $data);
        return response()->json(["success" => true, "user" => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //soft delete user the redirect to users page no need for delete page
         //delete with delete request ( _method -> delete in submit form)
         // Search for soft delete flag, db column
        User::find($id)->delete();

        //redirect to users list page
        $users = User::all();
        $data = array(
                "users" => $users
            );

        //return view("users", $data);
        return response()->json(["success" => true]);
    }
}
