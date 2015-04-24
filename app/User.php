<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * The attributes that have dates type column
	 */
	protected $dates = ['last_login'];

	/**
	 * Dengan table userprofiles berelasi ONE TO ONE
	 */
	public function userprofile() {
		return $this->hasOne('App\Userprofile', 'user_id', 'id');
	}

	/**
	 * Get user record by login (username or email) with scope
	 *
	 * @param	string
	 * @return	object
	 */
	public function scopeGetUserByLogin($query, $login) {
		return $query->where('username', '=', strtolower($login))
			->orWhere('email', '=', strtolower($login));
	}

	/**
	 * Get user record by username with scope
	 *
	 * @param	string
	 * @return	object
	 */
	public function scopeGetUserByUsername($query, $username) {
		return $query->where('username', '=', strtolower($username));
	}

	/**
	 * Get user record by email with scope
	 *
	 * @param	string
	 * @return	object
	 */
	public function scopeGetUserByEmail($query,$email) {
		return $query->where('email', '=', strtolower($email));
	}

	/**
	 * Get user record by Id with scope
	 *
	 * @param	int
	 * @param	bool
	 * @return	object
	 */
	public function scopeGetUserById($query, $user_id, $activated) {
		return $query->where('id', '=', $user_id)
			->where('activated', '=', $activated ? 1 : 0);
	}

	/**
	 * Check if username available for registering
	 * with scope
	 *
	 * @param	string
	 * @return	bool
	 */
	public function scopeIsUsernameAvailable($query,$username) {

		return $query->whereRaw('LOWER(username)= ?', array(strtolower($username)));
	}

	/**
	 * Check if email available for registering
	 * with scope
	 *
	 * @param	string
	 * @return	bool
	 */
	public function scopeIsEmailAvailable($query, $email) {
		return $query->whereRaw('LOWER(email)=? OR LOWER(new_email)=?', array(strtolower($email), strtolower($email)));
	}

	/**
	 * Create new user record
	 * with scope
	 *
	 * @param	array
	 * @param	bool
	 * @return	array
	 */
	public static function createUser($data, $activated = TRUE) {

		/* new user profile */
		$userprofile = new Userprofile;
		$userprofile->fullname = $data['fullname'];

		/* new user */
		$data['activated'] = $activated ? 1 : 0;

		$user = New User;
		$user->username = $data['username'];
		$user->email = $data['email'];
		$user->password = $data['password'];
		$user->banned = 0;
		$user->last_ip = $data['last_ip'];
		//$user->last_login = date('Y-m-d H:i:s');
		$user->new_email_key = isset($data ['new_email_key']) ? $data ['new_email_key'] : NULL;
		$user->activated = $data['activated'];
		$user->save();
		$user->userprofile()->save($userprofile);
		return array('user_id' => $user->id);

	}

	/**
	 * Purge table of non-activated users
	 * with scope
	 *
	 * @param	int
	 * @return	void
	 */
	public function scopePurgeNa($query, $time_expire_periode) {
		return $query->where('activated', '=', 0)->where('created_at', '<', $time_expire_periode);
	}

	/**
	 * Activate user if activation key is valid.
	 * Can be called for not activated users only.
	 * with scope
	 *
	 * @param	int
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	public static function activateUser($user_id, $activation_key, $activate_by_email) {
		$user = User::where('id', '=', $user_id)
			->where('activated', '=', 0)
			->where($activate_by_email ? 'new_email_key' : 'new_password_key', '=', $activation_key)
			->first();
		if(!is_null($user)) {
			$user->activated = 1;
			$user->new_email_key = NULL;
			$user->save();

			if (!$user->userprofile()->count()) {
				$profile = New Userprofile;
				$profile->fullname = $user->username;
				$user->userprofile()->save($profile);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if given password key is valid and user is authenticated.
	 * with scope
	 *
	 * @param	int
	 * @param	string
	 * @param	int
	 * @return	void
	 */
	public static function scopeCanResetPassword($query, $user_id, $new_pass_key, $time_expire_periode) {
		return $query->where('new_password_key', '=', $new_pass_key)
			->where('new_password_requested', '>', $time_expire_periode);

	}

	/**
	 * Change user password if password key is valid and user is authenticated.
	 * with scope
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	public static function resetPassword($user_id, $new_pass, $new_pass_key, $time_expire_periode) {

		$user = User::where('id', '=', $user_id)
			->where('new_password_key', '=', $new_pass_key)
			->where('new_password_requested', '>=', $time_expire_periode)
			->first();
		/*
          $user = User::whereIdAndNewPasswordKeyAndNewPasswordRequested($user_id, $new_pass_key, $time_expire_periode)
          ->first();
         */
		$user->password = $new_pass;
		$user->new_password_key = NULL;
		$user->new_password_requested = NULL;
		$user->save();
		//return $user->count() > 0;
		return $user;
	}

	/**
	 * Set new email for user (may be activated or not).
	 * The new email cannot be used for login or notification before it is activated.
	 * with scope
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	public static function setNewEmail($user_id, $new_email, $new_email_key, $activated) {
		$user = User::where('id', '=', $user_id)
			->where('activated', '=', $activated ? 1 : 0)
			->first();
		$email_or_new_email = $activated ? 'new_email' : 'email';
		$user->$email_or_new_email = $new_email;
		$user->new_email_key = $new_email_key;
		$user->save();

		return $user->count() > 0;
	}

}
