<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'login_attempts';

    /**
     * Get number of attempts to login occured from given IP-address or login
     *
     * @param	string
     * @param	string
     * @return	int
     */
    public static function scopeGetAttemptsNum($query, $ip_address, $login) {
        $query = $query->where('ip_address', '=', $ip_address);
        if (strlen($login) > 0) {
            $query->orWhere('login', '=', $login);
        }

        return $query;
    }

}
