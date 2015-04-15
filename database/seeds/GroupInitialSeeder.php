<?php
/**
 * Created by PhpStorm.
 * User: Bana
 * Date: 3/17/2015
 * Time: 2:13 PM
 */
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Group;

class GroupInitialSeeder extends Seeder
{
    public function run()
    {
        DB::table('groups')->truncate();

        Group::create(array(
            'name' => 'Registered',
            'created_at' => time(),
            'updated_at' => time(),
        ));

        Group::create(array(
            'name' => 'Admin',
            'created_at' => time(),
            'updated_at' => time(),
        ));
    }
}