<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Property;
use App\SessionProperty;
use Validator;

use App\Province;
use App\City;
use App\SessionPropertyTags;
use App\SessionPropertyImages;
use App\PropertyImages;
use App\PropertyTags;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Log;
use DateTime;
use DB;

class PropertyController extends Controller
{
    /* File upload directory */
    protected $upload_directory = "../upload/" ;

    protected function imageValidation(Request $request, $image_attribute, &$image_error, &$errorMessage)
    {
       //validate image  
        $image_error = false;
        $errorMessage = "";
        $file_name = null;

        //dd($request->file())
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
                if($image->getClientSize() < $image->getMaxFilesize())
                {
                  //create new filename
                  $randomnumber = mt_rand ();
                  $randomname =   mt_rand ();
                  $hashedrandomname = md5($randomname);
                  $hashedrandomname2 = md5($hashedrandomname);
                  $file_name = $hashedrandomname2.".".$file_extension;
                  //$file_name = substr($file_name, 0, $hashLength);
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

        return $file_name;
    }

    //overload
    protected function imageValidationFileParam($image, &$image_error, &$errorMessage)
    {
       //validate image  
        $image_error = false;
        $errorMessage = "";
        $file_name = null;

        $filename  = $image->getClientOriginalExtension()."-".time();
        
        $valid_file_extensions = array("jpg", "jpeg", "gif", "png");
        $file_extension = $image->guessExtension();//strrchr($image->getClientOriginalExtension(), ".");

        if (in_array($image->guessExtension(), $valid_file_extensions) ) 
        {
            
          if ($image->getError() == UPLOAD_ERR_OK && $image->isValid())
          {
            if($image->getClientSize() < $image->getMaxFilesize())
            {
              //create new filename
              $randomnumber = mt_rand ();
              $randomname =   mt_rand ();
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

        return $file_name;
    }

     /**
     * Get a validator for an incoming user requests.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorStep1(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'property_type' => 'required|max:255',
            'property_condition' => 'required|max:255',
            'property_description' => 'required|max:255',
        ]);
    }

    protected function validatorStep2(array $data)
    {
        return Validator::make($data, [
            'city' => 'required|max:255',
            'province' => 'required|max:255',
        ]);
    }

    protected function validatorStep3(array $data)
    {
        return Validator::make($data, [
            'selling_price' => 'required|numeric',
            'land_size' => 'required|numeric',
            'bedrooms' => 'required',
            'bathrooms' => 'required',
            'parkings' => 'required',
            'certificate' => 'required|max:255',
            'orientation' => 'required|max:255',
            'furnish_condition' => 'required|max:255',
        ]);
    }

    protected function validatorStep4(array $data)
    {
        return Validator::make($data, [
            'cover_image' => 'required|max:'.UploadedFile::getMaxFilesize(),
        ]);
    }

    protected function validator(array $data)
    {
    	return Validator::make($data, [
            'name' => 'required|max:255',
            'property_type' => 'required|max:255',
            'property_condition' => 'required|max:255',
            'property_description' => 'required|max:255',
            'city' => 'required|max:255',
            'province' => 'required|max:255',
            'selling_price' => 'required|max:255',
            'land_size' => 'required|max:255',
            'bedrooms' => 'required|max:255',
            'bathrooms' => 'required|max:255',
            'parkings' => 'required|max:255',
            'certificate' => 'required|max:255',
            'orientation' => 'required|max:255',
            'furnish_condition' => 'required|max:255',
        ]);
    }

    //email is the unique identifier since id isn't available for security
    protected function updateValidator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'property_type' => 'required|max:255',
            'property_condition' => 'required|max:255',
            'property_description' => 'required|max:255',
            'city' => 'required|max:255',
            'province' => 'required|max:255',
            'selling_price' => 'required|max:255',
            'land_size' => 'required|max:255',
            'bedrooms' => 'required|max:255',
            'bathrooms' => 'required|max:255',
            'parkings' => 'required|max:255',
            'certificate' => 'required|max:255',
            'orientation' => 'required|max:255',
            'furnish_condition' => 'required|max:255',
        ]);
    }

    //multipage form validation
    protected function formStep1(Request $request)
    {
        //validate data
        $validator = $this->validatorStep1($request->all());
        //check if validation fails
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        
        //store in session table
        $propertySession = SessionProperty::Create($request->toArray());
        //redirect to second page with current session values ( just name is enough)
        $data = [
        	"propertySession" => $propertySession,
        ];

        //Store in session
        session(['propertyFormProgress' => $propertySession]);
        return view("addproperty-2",$data);
    }

    protected function formStep2(Request $request)
    { 
    	if(session("propertyFormProgress") == null)
    	{
    		return redirect("addproperty");
    	}

        //validate data
        $validator = $this->validatorStep2($request->all());
        //check if validation fails
        if ($validator->fails()) {
        	$data = [
	        	"propertySession" => $request->propertySession,
	        	"errors" => $validator->errors(),
	        ];

        	$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
            return view("addproperty-2",$data);
        }

        $data = $request->toArray();

        $propertyName = session("propertyFormProgress")->name;
        
        //UPDATE session table with extra values
        $sessionProperty = SessionProperty::find(session("propertyFormProgress")->id);

        if($sessionProperty != null)
        	$sessionProperty->update($data);

        //UPDATE session
        session(['propertyFormProgress' => $sessionProperty]);
        
         $data = [
        	"propertySession" => $sessionProperty,
        ];

        //redirect to 3rd page with current session values 
        return view("addproperty-3", $data);
    }

    protected function formStep3(Request $request)
    {
    	if(session("propertyFormProgress") == null)
    	{
    		return redirect("addproperty");
    	}

        //validate data
        $validator = $this->validatorStep3($request->all());
        //check if validation fails
        //dd($validator->errors()->getMessages());
        if ($validator->fails()) {
        	$data = [
        	"propertySession" => $request->propertySession,
        	"errors" => $validator->errors(),
        ];

        	$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
            return view("addproperty-3",$data);
        }


        //dd($request->toArray());
        $data = $request->toArray();
        //UPDATE session table with extra values
        $sessionProperty = SessionProperty::find(session("propertyFormProgress")->id);

        if($sessionProperty != null)
        	$sessionProperty->update($data);

        //UPDATE session
        session(['propertyFormProgress' => $sessionProperty]);
        
         $data = [
        	"propertySession" => $sessionProperty,
        ];

        //handling tags
        foreach($request->tags as $tag)
        {
        	SessionPropertyTags::Create(["tag_id" => $tag, "property_id" => session("propertyFormProgress")->id]);
        }

       //retrieve tags from request, then update propertyTags session
        
        //redirect to 3rd page with current session values 
        return view("addproperty-4", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store2(Request $request)
    {
    	if(session("propertyFormProgress") == null)
    	{
    		return redirect("addproperty");
    	}

   		//!! validate/check all values to make sure no step is skipped
        //validate data
        $validator = $this->validatorStep4($request->all());
        //check if validation fails
        //dd($validator->errors()->getMessages());
        if ($validator->fails()) {
        	$data = [
        	"propertySession" => $request->propertySession,
        	"errors" => $validator->errors(),
        	//"input" => $request,
        ];

        	$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
            return view("addproperty-4",$data);
        }

        $data = $request->toArray();

        

        //main image is added, loop on all images and validate manually for security
        $file_name = $this->imageValidation($request,'cover_image',$image_error,$errorMessage);
                Log::info("Image validation returned");
                Log::info($image_error);
                Log::info($errorMessage);
        if($image_error)
        {
        	$validator = Validator::make([],[]);
            $validator->errors()->add('cover_image', $errorMessage);
            //dd($validator->errors());
            $data = [
	        	"propertySession" => $request->propertySession,
	        	"errors" => $validator->errors(),
	        	//"input" => $request,
	        ];

        	$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
            return view("addproperty-4",$data);
        }

        if($file_name != null && $file_name != "")
        {
            $data["cover_image"] =  $this->upload_directory.$file_name;
            //store in propertyImage db
            SessionPropertyImages::create(["src" => $data["cover_image"], "property_id" => session("propertyFormProgress")->id, "cover" =>true]);
        }

        $index = 0;
        //dd($request->ad_image);
        if($request->ad_image != null)
        {
	        foreach($request->ad_image as $image)
	        {
	        	$file_name = $this->imageValidationFileParam($image,$image_error,$errorMessage);
                Log::info("Image validation file param returned");
                Log::info($image_error);
                Log::info($errorMessage);
	        	if($image_error)
		        {
		        	$validator = Validator::make([],[]);
		            $validator->errors()->add('ad_image_error', $errorMessage);
		            //dd($validator->errors());
		            $data = [
			        	"propertySession" => $request->propertySession,
			        	"errors" => $validator->errors(),
			        ];

		        	$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
		            return view("addproperty-4",$data);
		        }

	        	if($file_name != null && $file_name != "")
	        	{
	            	//store in propertyIamge db
	            	SessionPropertyImages::create(["src" => $this->upload_directory.$file_name, "property_id" => session("propertyFormProgress")->id]);
	            }
	        }
	    }

        //UPDATE session table with extra values
        $sessionProperty = SessionProperty::find(session("propertyFormProgress")->id);

        if($sessionProperty != null)
        	$sessionProperty->update($data);

        //UPDATE session
        session(['propertyFormProgress' => $sessionProperty]);

		//validate all steps data
        $validator = $this->validator(session("propertyFormProgress")->toArray());

        //dd($validator->errors());
        //check if validation fails
        //dd($validator->errors()->getMessages());
        if ($validator->fails()) {
        	$validator->errors()->add("missingData", "Make sure all data are added");
        	$data = [
	        	"propertySession" => $request->propertySession,
	        	"errors" => $validator->errors(),
	        	//"input" => $request,
	        ];

        	//$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
            //return view("addproperty",$data);
            return redirect("addproperty")->withInput()->with("errors",$validator->errors());
        }

        
        //var_dump($sessionProperty->images);
        	

        //copy sessiontables data to actual tables
        $propertyAdded = Property::create(session("propertyFormProgress")->toArray()); //has extra data shouldn't be a problem

        //add images
        foreach($sessionProperty->images as $image)
        {
        	PropertyImages::create(["src" => $image->src, "property_id" => $propertyAdded->id, "cover" => $image->cover]);
        }

        //add tags
        foreach($sessionProperty->tags as $tag)
        {
        	PropertyTags::create(["tag_id" => $tag->tag_id, "property_id" => $propertyAdded->id]);
        }



        //delete from session db ( al 3 session tables)
        SessionPropertyImages::where("property_id",session("propertyFormProgress")->id)->delete();
        SessionPropertyTags::where("property_id",session("propertyFormProgress")->id)->delete();
        SessionProperty::where("id",session("propertyFormProgress")->id)->delete(session("propertyFormProgress")->id);

        //clear session
        session(['propertyFormProgress' => null]);

        return redirect("property");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info($request);
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

        //store data
        $images = [];
        $tags = [];
        //main image is added, loop on all images and validate manually for security
        $file_name = $this->imageValidation($request,'cover_image',$image_error,$errorMessage);
        if($image_error)
        {
            $validator = Validator::make([],[]);
            $validator->errors()->add('cover_image', $errorMessage);
            //dd($validator->errors());
            $data = [
                "propertySession" => $request->propertySession,
                "errors" => $validator->errors(),
                //"input" => $request,
            ];


            $request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
            //return view("addproperty-4",$data);
            return response()->json($data);
        }


        if($file_name != null && $file_name != "")
        {
            $data["cover_image"] =  $this->upload_directory.$file_name;
            
        }

        $index = 0;
        $otherImagesSRC = [];
        //dd($request->ad_image);
        if($request->ad_image != null)
        {
            //dd($request->file());
            foreach($request->ad_image as $image)
            {
                //dd($image);
                $file_name = $this->imageValidationFileParam($image,$image_error,$errorMessage);
                //dd($file_name);
                if($image_error)
                {
                    $validator = Validator::make([],[]);
                    $validator->errors()->add('ad_image_error', $errorMessage);
                    //dd($validator->errors());
                    $data = [
                        "propertySession" => $request->propertySession,
                        "errors" => $validator->errors(),
                        //"input" => $request,
                    ];

                    $request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
                    //return view("addproperty-4",$data);
                    return response()->json($data);
                }

                if($file_name != null && $file_name != "")
                {

                    //store in propertyIamge db
                    $otherImagesSRC[]  = $this->upload_directory.$file_name;                   
                }
            }
        }


            

        //add data to property
        $propertyAdded = Property::create($request->toArray()); //has extra data shouldn't be a problem
        
        //store images
        $images[] = PropertyImages::create(["src" => $data["cover_image"], "property_id" => $propertyAdded->id, "cover" =>true]);
        foreach($otherImagesSRC as $imageSRC)
            $images[] = PropertyImages::create(["src" => $imageSRC, "property_id" => $propertyAdded->id]);

        //add tags
        Log::info($request->tags);
        if($request->tags != null)
        {
            for($i = 0; $i<count($request->tags); $i++)
            {
                Log::info($request->tags[$i]);
                $tags[] = PropertyTags::create(["tag_id" => $request->tags[$i]['id'], "property_id" => $propertyAdded->id]);
            }
        }


        //return redirect("property");
        $data = [
            "property" => $propertyAdded,
            "images" => $images,
            "tags" => $tags,
            "success"=>true,
            "owner" => $propertyAdded->user
        ];
        return response()->json($data);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $properties = Property::all();



        $data = [];
        if($properties != null)
        foreach($properties as $property)
        {
            $data[] = ["property" => $property, "owner" => $property->user, "images" => $property->images,
            "tags" => $property->tags,];
        }

        //return view("properties", $data);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $property = Property::find($id);

        $data = [];

        if($property != null)
        {
            $data = array(
                    "property" => $property,
                    "images" => $property->images,
                    "tags" => $property->tags,
                );
        }

        //return view("property", $data);
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
        $property = Property::find($id);
        $data = array(
                	"property" => $property
                );

        return view("editproperty", $data);
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
        //validate data
        $property = Property::find($id);
        $validator = $this->updateValidator($request->all());
        //check if validation fails
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        
        $data = $request->toArray();

        //dd($data);

        //tags, remove and add selected
        $storedTags = PropertyTags::where("property_id", $id);
        if($storedTags != null)
        	$storedTags->delete();

        foreach($request->tags as $tag)
        {
        	PropertyTags::Create(["tag_id" => $tag, "property_id" => $id]);
        }


        //images:
        // add cover image if exist
        if(isset($data["cover_image"]) && $data["cover_image"] == "")
          $data["cover_image"] = $property->cover_image;
        else{
			//file upload validation
			$file_name = $this->imageValidation($request,'cover_image',$image_error,$errorMessage);

          	if($image_error)
	        {
	        	$validator = Validator::make([],[]);
	            $validator->errors()->add('cover_image', $errorMessage);
	            //dd($validator->errors());
	            $data = [
		        	"errors" => $validator->errors(),
		        	"property" => $property,
		        	//"input" => $request,
		        ];

	        	$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
	            return view("editproperty",$data);
	        }
			//updating cover image
			if($file_name != null && $file_name != "")
			{
				$data["cover_image"] =  $this->upload_directory.$file_name;
				//update cover_image in db
				PropertyImages::where("property_id", $id)->where("cover",true)->update(["src" => $this->upload_directory.$file_name ]);

			}
          else
            $data["cover_image"] = $property->cover_image;
        }

        // add images if exist
        if($request->ad_image != null)
        {
        	//dd($request->file());
	        foreach($request->ad_image as $image)
	        {
	        	//dd($image);
	        	$file_name = $this->imageValidationFileParam($image,$image_error,$errorMessage);
	        	//dd($file_name);
	        	if($image_error)
		        {
		        	$validator = Validator::make([],[]);
		            $validator->errors()->add('ad_image_error', $errorMessage);
		            //dd($validator->errors());
		            $data = [
			        	"errors" => $validator->errors(),
			        	"property" => $property,
			        ];

		        	$request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
		            return view("editproperty",$data);
		        }

	        	if($file_name != null && $file_name != "")
	        	{
	            	//store in propertyIamge db
	            	PropertyImages::create(["src" => $this->upload_directory.$file_name, "property_id" => $id]);
	            }
	        }
	    }

        // update values
        $property->update($data);

        //return to property
        $data = array(
                "property" => $property
            );

        return redirect("property/$id")->with("property", $property);
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
        //validate data
        $property = Property::find($id);


         Log::info($request);
        $validator = $this->updateValidator($request->all());
        //check if validation fails
        //dd($validator->errors()->getMessages());
        if ($validator->fails()) {
           // Log::error($validator->errors());
          $data = [
            'input' => $request,
            'errors' => $validator->errors()
          ];

          return response()->json($data);
            $this->throwValidationException(
                $request, $validator
            );
        }

        
        $data = $request->toArray();

        //dd($data);

        //tags, remove and add selected
        $storedTags = PropertyTags::where("property_id", $id); 
        if($storedTags != null)
            $storedTags->delete();

        $tags = [];
        if($request->tags != null)
        foreach($request->tags as $tag)
        {
            Log::info($tag);
            $tags[] = PropertyTags::Create(["tag_id" => $tag['tag_id'], "property_id" => $id]);
            //PropertyTags::Create($tag);
        }


        //images:
        // add cover image if exist
        if(isset($data["cover_image"]) && $data["cover_image"] == "")
          $data["cover_image"] = $property->cover_image;
        else{
            //file upload validation
            $file_name = $this->imageValidation($request,'cover_image',$image_error,$errorMessage);

            if($image_error)
            {
                $validator = Validator::make([],[]);
                $validator->errors()->add('cover_image', $errorMessage);
                //dd($validator->errors());
                $data = [
                    "errors" => $validator->errors(),
                    "property" => $property,
                    //"input" => $request,
                ];

                $request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
                //return view("editproperty",$data);
                return response()->json($data);
            }
            //updating cover image
            if($file_name != null && $file_name != "")
            {
                $data["cover_image"] =  $this->upload_directory.$file_name;
                //update cover_image in db
                PropertyImages::where("property_id", $id)->where("cover",true)->update(["src" => $this->upload_directory.$file_name ]);

            }
          else
            $data["cover_image"] = $property->cover_image;
        }

        //$images = [];
        $images = $property->images;
        // add images if exist
        if($request->ad_image != null)
        {
            //dd($request->file());
            foreach($request->ad_image as $image)
            {
                //dd($image);
                $file_name = $this->imageValidationFileParam($image,$image_error,$errorMessage);
                //dd($file_name);
                if($image_error)
                {
                    $validator = Validator::make([],[]);
                    $validator->errors()->add('ad_image_error', $errorMessage);
                    //dd($validator->errors());
                    $data = [
                        "errors" => $validator->errors(),
                        "property" => $property,
                    ];

                    $request->flash(); // can replace this with redirect("page")->withInput() but only for redirect no views
                    //return view("editproperty",$data);
                    return response()->json($data);
                }

                if($file_name != null && $file_name != "")
                {
                    //$data['ad_image['.$index.']'] =  $this->upload_directory.$file_name;
                    //store in propertyIamge db
                    $images[]= PropertyImages::create(["src" => $this->upload_directory.$file_name, "property_id" => $id]);
                }
            }
        }

        // update values
        $property->update($data);

        //return to property
        $data = array(
                "success" => true,
                "property" => $property,
                "images" => $images,
                "tags" => $tags,
                "owner" => $property->user,
            );

        //return redirect("property/$id")->with("property", $property);
        return response()->json($data);
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
         //delete with delete reuqest ( _method -> delete in submit form)
         // Search for soft delete flag, db column
    	if(Property::find($id) != null)
       		Property::find($id)->delete();

        //redirect to users list page

        $properties = Property::all();
        $data = array(
                "properties" => $properties
            );

        //return view("properties", $data);
        return response()->json(["success" => true]);
    }

    /**
     * Remove specific property image (called though ajax)
     *
     * @param  int  $id (propertyImage id)
     * @return \Illuminate\Http\Response
     */
    public function destroyImage($id)
    {

    	if(PropertyImages::find($id) != null)
       		PropertyImages::find($id)->delete();

        return response()->json(["success"=>true]);
    }

    public function declineProperty($id)
    {
        $property = Property::find($id);
        if($property != null)
            $property->update(["status" => "declined"]);

        return response()->json(["success" => true]);
    }

    public function acceptProperty($id)
    {
        $date = new DateTime();
        
        Log::info($date->getTimestamp());
        $property = Property::find($id);
        if($property != null)
        {
            DB::UPDATE("update property set status = 'accepted', accepted_at = NOW() where id= ?",[$id]);
            //send email
            $to = $property->user->email;
            $subject = "Your Property has now been accepted! - ".$property->name;
            $message= "<br/>Thank you for listing your property. Your property has now been confirmed and listed. With Rezo you have access to beautiful properties in Jakarta. Let's see the ad ".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.html#/app/properties/view/".$id; 
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
            Log::info("email: ".$message);
            $mailsent = mail($to,$subject,$message,$headers);
            if($mailsent)
                $formSuccess = true;
            else
                Log::info("An error occurred while sending approval email");

            Log::info($message);
        }

        return response()->json(["success" => true]);
    }
}
