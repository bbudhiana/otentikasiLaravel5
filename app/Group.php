<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'groups';


    /*
     * relasi dengan table userprofiles
     */
    public function userprofiles()
    {
        return $this->hasMany('App\Userprofile');
    }

}
