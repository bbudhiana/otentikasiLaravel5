<?php
/**
 * Created by PhpStorm.
 * User: Bana
 * Date: 3/17/2015
 * Time: 2:13 PM
 */
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Userprofile;

class UserInitialSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->truncate();
        DB::table('userprofiles')->truncate();

        if (!User::whereUsername('admin')->count()) {
            $user = User::create(array(
                    'username' => 'admin',
                    'email' => 'admin@your-site.com',
                    'password' => Hash::make('admin123'),
                    'activated' => 1,
                    'banned' => 0,
                    'ban_reason' => NULL,
                    'new_password_key' => NULL,
                    'new_password_requested' => NULL,
                    'new_email' => NULL,
                    'new_email_key' => NULL,
                    'last_ip' => '::1',
                    'last_login' => date('Y-m-d H:i:s'),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'remember_token' => NULL)
            );
            Userprofile::create(array(
                'fullname' => 'Administrator Site',
                'user_id' => $user->id,
                'group_id'=> 2,
                'created_at' => time(),
                'updated_at' => time(),
            ));
        }
    }
}