<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      Setting::create([
        'name' => 'Test Admin',
        'slogan' => 'Admin Dashboard',
        'reg' => '12345',
        'stablished' => '2021',
        'email' => 'abc@abc.com',
        'contact' => '123456798',
        'address' => 'USA,America',
        'website' => 'http://www.google.com',
        'logo' => 'assets/images/logo/default.png',
        'layout' => '1',
        'running_year' => '2021',
        'created_at' => date('Y-m-d'),
        'updated_at' => date('Y-m-d')
      ]);
   }
}
