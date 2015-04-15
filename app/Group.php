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
     * relation one to many with users
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }

}
