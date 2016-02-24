<?php

use Illuminate\Database\Seeder;
use App\Country;
use App\Province;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);

        //add master admin
        DB::table('users')->insert([
            'first_name' => "Master",
            'last_name' => "Admin",
            'email' => "admin@admin.com",
            "is_admin" => true,
            'password' => bcrypt('password'),
            'mobile_number' => "123",
            "user_type" => 4,
            "created_at" => date('Y-m-d'),
            "updated_at" => date('Y-m-d')
        ]);

        DB::table("Country")->insert(["name" => "Indonesia"]);
        $country = Country::where('name', "Indonesia")->first();
        

        $csvFilePath = public_path().'\province.csv';
             $csvFile = file($csvFilePath);
        $data = [];
        $provinceData = [];

        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line);
            $provinceData[] = ["name" => str_getcsv($line)[0], "country_id" => $country->id ];
        }

        $csvFilePath = public_path().'\cities.csv';
             $csvFile = file($csvFilePath);
        $data = [];
        foreach ($csvFile as $line) {
            $data[] = str_getcsv($line);
        }


         DB::table("Province")->insert($provinceData);

        DB::table("City")->insert([
	        	["province_id" => Province::where('name', "Jakarta")->first()->id, "name"=>"Jakarta Raya"],
	        	["province_id" => Province::where('name', "East Java")->first()->id, "name"=>"Surabaya"]
        	]);

        //Insert user types general,agency,admin,master
        DB::table("UserType")->insert([
            ["name" => "general"],
            ["name" => "agency"],
            ["name" => "admin"],
            ["name" => "master"]
        ]);

        //Insert tags ( read from file)
        DB::table("Tags")->insert([
            ["name" => "Minimalist"],
            ["name" => "Modern"],
            ["name" => "Balcony"],
            ["name" => "Rooftop"],
            ["name" => "Swimming Pool"],
            ["name" => "Garden"],
            ["name" => "Gazebo"],
            ["name" => "Mezzanine"],
            ["name" => "Luxury"],
            ["name" => "Second Hand"],
            ["name" => "Cluster"],
            ["name" => "Sea View"],
            ["name" => "Front Yard"],
            ["name" => "Basement Parking"],
            ["name" => "Fish Pond"],
            ["name" => "Paintings"],
            ["name" => "Ethnic"],
        ]);
    }
}
