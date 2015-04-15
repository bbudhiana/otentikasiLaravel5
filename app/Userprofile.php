<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Userprofile extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'userprofiles';

    /**
     * Dengan table user berelasi ONE TO ONE
     */
    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
