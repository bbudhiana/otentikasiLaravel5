<?php namespace App\Bunciono\Libraries;

use Config;
use Request;
use Lang;
use Carbon;
use Mail;
use App\LoginAttempt;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Console\Tinker\Presenters\IlluminateApplicationPresenter;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;

define('STATUS_ACTIVATED', 1);
define('STATUS_NOT_ACTIVATED', 0);
define('LOGIN_BY_USERNAME', (Config::get('otentikasi.login_by_username') and Config::get('otentikasi.use_username')));
define('LOGIN_BY_EMAIL', Config::get('otentikasi.login_by_email'));

class Otentikasi
{
    /**
     * error message
     */
    static $error = array();

    /**
     * Clear all attempt records for given IP-address and login
     * (if attempts to login is being counted)
     *
     * @param    string
     * @return    void
     */
    private function clearLoginAttempts($login)
    {
        if (Config::get('otentikasi.login_count_attempts')) {
            $expire_period = Config::get('otentikasi.login_attempt_expire') == 0 ? 86400 : Config::get('otentikasi.login_attempt_expire');
            $size_expired = time() - $expire_period;
            $time_expire_periode = Carbon\Carbon::createFromTimestamp($size_expired);

            LoginAttempt::where('ip_address', '=', Request::getClientIp())
                ->orWhere('login', '=', $login)
                /* Purge obsolete login attempts */
                ->orWhere('time', '<', $time_expire_periode)
                ->delete();
        }
    }

    /**
     * Update user login info, such as IP-address or login time, and
     * clear previously generated (but not activated) passwords.
     * with scope
     *
     * @param    int
     * @param    bool
     * @param    bool
     * @return    void
     */
    private function updateLoginInfo($user, $record_ip, $record_time)
    {
        $user->new_password_key = NULL;
        $user->new_password_requested = NULL;
        if ($record_ip)
            $user->last_ip = Request::getClientIp();
        if ($record_time)
            $user->last_login = Carbon\Carbon::now();
        $user->save();
    }

    /**
     * Increase number of attempts for given IP-address and login
     * (if attempts to login is being counted)
     *
     * @param    string
     * @return    void
     */
    private function increaseLoginAttempt($login)
    {
        if (Config::get('otentikasi.login_count_attempts')) {
            $login_attempt = new LoginAttempt;
            $login_attempt->ip_address = Request::getClientIp();
            $login_attempt->login = $login;
            $login_attempt->save();
            //LoginAttempts::increaseAttempt(Request::getClientIp(), $login);
        }
    }

    /**
     * Get error message.
     * Can be invoked after any failed operation such as login or register.
     *
     * @return    string
     */
    private static function getErrorMessage()
    {
        /* put error message on the messages bag */
        $messages = new MessageBag();
        foreach (self::$error as $key => $value) {
            $messages->add($key, $value);
        }
        return $messages;
    }

    /**
     * Check the validation form of login
     *
     * $param array
     * $return object
     */
    public function loginValidation($datalogin)
    {

        /* Which rules to use to validation (based on config) */
        if (LOGIN_BY_USERNAME and LOGIN_BY_EMAIL) {
            $rules['login'] = 'required';
        } else if (LOGIN_BY_USERNAME) {
            $rules['login'] = 'required';
        } else {
            $rules['login'] = 'required|email';
        }

        $rules['password'] = 'required|alphaNum|min:3';

        /* Get login for counting attempts to login */
        if (Config::get('otentikasi.login_count_attempts') and ($login_attempt = $datalogin['login'])) {
            $login_attempt = filter_var($login_attempt, FILTER_SANITIZE_STRING);
        } else {
            $login_attempt = '';
        }

        if ($this->isMaxLoginAttemptsExceeded($login_attempt)) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        /* run the validation rules on the inputs from the form */
        $validator = Validator::make($datalogin, $rules);

        /* this validation is from database perspective */
        $validator->after(function ($validator) use ($datalogin) {
            $this->loginDbInvalid($validator, $datalogin);
        });

        return $validator;
    }

    /**
     * Check if login invalid in DB perspective
     *
     * @param string
     * @return bool
     */
    private function loginDbInvalid($validator, $datalogin)
    {
        if ((strlen($datalogin['login']) > 0) and (strlen($datalogin['password']) > 0)) {

            /* Which function to use to login (based on config) */
            if (LOGIN_BY_USERNAME and LOGIN_BY_EMAIL) {
                $get_user_func = 'getUserByLogin';
            } else if (LOGIN_BY_USERNAME) {
                $get_user_func = 'getUserByUsername';
            } else {
                $get_user_func = 'getUserByEmail';
            }

            $datauser = User::$get_user_func($datalogin['login']);

            if (!is_null($user = $datauser->first())) { // login ok

                /* assign data login to array */
                $login_data = array(
                    'password' => $datalogin['password'],
                    //'activated' => 1,
                    'banned' => 0
                );

                /* check what is login ? email or username */
                if (!filter_var($datalogin['login'], FILTER_VALIDATE_EMAIL)) {
                    $login_data['username'] = $user->username;
                } else {
                    $login_data['email'] = $user->email;
                }

                /* Jika ternyata tidak valid, maka dicari sumber tidak validnya */
                if (!Auth::attempt($login_data, isset($datalogin['remember']) ? TRUE : FALSE)) {
                    $this->increaseLoginAttempt($datalogin['login']);
                    if ($user->banned == 1) { // fail - banned
                        $validator->errors()->add('banned', Lang::get('otentikasi.banned'));
                    } else if ($user->activated == 0) { // fail - not activated
                        $validator->errors()->add('not_activated', Lang::get('otentikasi.not_activated'));
                    } else if (!Hash::check($datalogin['password'], $user->password)) { // fail - wrong password
                        $validator->errors()->add('incorrect_password', Lang::get('otentikasi.incorrect_password'));
                    } else { //jika bukan karena hal di atas, maka gunakan default pengembalian error
                        $validator->errors()->add('incorrect_login', Lang::get('otentikasi.incorrect_login'));
                    }
                }
            } else { // fail - wrong login
                $this->increaseLoginAttempt($datalogin['login']);
                $validator->errors()->add('incorrect_login', Lang::get('otentikasi.incorrect_login'));
            }
        }
    }

    /**
     * Login success on the site.
     * when validation process do not result error
     *
     * @param    array (username or email or both depending on settings in config file)
     * @return   string
     */
    public function loginSuccess($datalogin)
    {
        /* clear the login attempts */
        $this->clearLoginAttempts($datalogin['login']);

        /* upgrade password dari md5 ke milik laravel, berfungsi jika password sebelumnya adalah MD5 */
        if (md5($datalogin['password']) === Auth::user()->password) {
            Auth::user()->password = Hash::make($datalogin['password']);
            Auth::user()->save();
        }

        /* update user info login */
        $this->updateLoginInfo(Auth::user(), Config::get('otentikasi.login_record_ip'), Config::get('otentikasi.login_record_time'));
        $message = Lang::get('otentikasi.success_login');
        return $message;
    }

    /**
     * Check if login attempts exceeded max login attempts (specified in config)
     *
     * @param    string
     * @return    bool
     */
    public function isMaxLoginAttemptsExceeded($login)
    {
        if (Config::get('otentikasi.login_count_attempts')) {
            return LoginAttempt::getAttemptsNum(Request::getClientIp(), $login)->count() >= Config::get('otentikasi.login_max_attempts');
        }
        return FALSE;
    }

    /**
     * Logout user from the site
     *
     * @return    string
     */
    public function logout()
    {
        Auth::logout();
        return Lang::get('otentikasi.success_logout');
    }

    /**
     * Check if user logged in. Also test if user is activated or not.
     *
     * @param    bool
     * @return    bool
     */
    public function isLoggedIn($activated = TRUE)
    {
        if (Auth::check()) {
            return Auth::user()->activated == ($activated ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED);
        }
        return FALSE;
    }

    /**
     * Check the register validation form of register
     *
     * $param array
     * $return object
     */
    public function registerValidation($dataregister)
    {
        /* Which rules to use to validation (based on config) */
        $rules['username'] = 'required|alpha_dash|unique:users'.'|min:'. Config::get('otentikasi.username_min_length').'|max:'. Config::get('otentikasi.username_max_length') ;
        $rules['email'] = 'required|email|unique:users';
        $rules['password'] = 'required|confirmed'.'|min:'. Config::get('otentikasi.password_min_length').'|max:'. Config::get('otentikasi.password_max_length');
        $rules['password_confirmation'] = 'required'.'|min:'. Config::get('otentikasi.password_min_length').'|max:'. Config::get('otentikasi.password_max_length');
        $rules['fullname'] = 'required';
        if (Config::get('otentikasi.captcha_registration')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }
        $rules['agree'] = 'accepted';

        /* run the validation rules on the inputs from the form */
        $validator = Validator::make($dataregister, $rules);

        /* this validation is from database perspective */
        $validator->after(function ($validator) use ($dataregister) {
            //$this->registerDbInvalid($validator, $dataregister);
        });

        return $validator;
    }

    /**
     * Check if login invalid in DB perspective
     *
     * @param string
     * @return bool
     */
    private function registerDbInvalid($validator, $dataregister)
    {
        if ((strlen($dataregister['username']) > 0) and !$this->isUsernameAvailable($dataregister['username'])) {
            $validator->errors()->add('username', Lang::get('otentikasi.username'));
        }
        if ((strlen($dataregister['email']) > 0) and !$this->isEmailAvailable($dataregister['email'])) {
            $validator->errors()->add('email', Lang::get('otentikasi.email'));
        }

    }

    /**
     * Register success on the site.
     * when validation process do not result error
     *
     * @param    array (username or email or both depending on settings in config file)
     * @return   string
     */
    public function registerSuccess($dataregister)
    {
        $email_activation = Config::get('otentikasi.email_activation');

        /* 1. Validasi lewat, maka siap create user baru */
        $dataregister = $this->createUser($dataregister['username'], $dataregister['email'], $dataregister['password'], $dataregister['fullname'], $email_activation);

        /* 2. Kirimkan lewat email berupa activasi atau ucapan selamat datang */
        $dataregister ['site_name'] = Config::get('otentikasi.website_name');

        if ($email_activation) { // send "activate" email
            $dataregister ['activation_period'] = Config::get('otentikasi.email_activation_expire') / 3600;
            $this->_sendEmail('activate', $dataregister ['email'], $dataregister);
            $message = Lang::get('otentikasi.registration_with_activation_success');

            unset($dataregister ['password']); // Clear password (just for any case)
        } else {
            if (Config::get('otentikasi.email_account_details')) { // send "welcome" email
                $this->_sendEmail('welcome', $dataregister ['email'], $dataregister);
            }
            $message = Lang::get('otentikasi.registration_success');

            unset($dataregister ['password']); // Clear password (just for any case)
        }
        return $message;
    }

    /**
     * check if register need captcha
     *
     * @return bool
     */
    public function isRegisterNeedCaptcha()
    {
        return Config::get('otentikasi.captcha_registration');
    }

    /**
     * Activate user using given key
     *
     * @param    string
     * @param    string
     * @param    bool
     * @return    string
     */
    public static function activateUser($user_id, $activation_key, $activate_by_email = TRUE)
    {
        $expire_period = Config::get('otentikasi.email_activation_expire') == 0 ? 172800 : Config::get('otentikasi.email_activation_expire');
        $size_expired = time() - $expire_period;
        $time_expire_periode = Carbon\Carbon::createFromTimestamp($size_expired);

        User::purgeNa($time_expire_periode)->delete();

        if ((strlen($user_id) > 0) and (strlen($activation_key) > 0)) {
            User::activateUser($user_id, $activation_key, $activate_by_email);
            return NULL;
        }
        self::$error = array('activation_failed' => Lang::get('otentikasi.activation_failed'));
        return self::getErrorMessage();
    }

    /**
     * Create new user on the site and return some data about it:
     * user_id, username, password, email, new_email_key (if any).
     *
     * @param    string
     * @param    string
     * @param    string
     * @param    bool
     * @return    array
     */
    public function createUser($username, $email, $password, $fullname, $email_activation)
    {
        $hashed_password = Hash::make($password);

        $data = array('username' => $username, 'password' => $hashed_password, 'email' => $email, 'fullname' => $fullname, 'last_ip' => Request::getClientIp());

        if ($email_activation) {
            $data ['new_email_key'] = md5(rand() . microtime());
        }
        if (!is_null($res = User::createUser($data, !$email_activation))) {
            $data ['user_id'] = $res ['user_id'];
            $data ['password'] = $password;

            unset($data ['last_ip']);
            return $data;
        }
        return NULL;
    }


    /**
     * Check if username available for registering.
     * Can be called for instant form validation.
     *
     * @param    string
     * @return    bool
     */
    private function isUsernameAvailable($username)
    {
        return ((strlen($username) > 0) and User::isUsernameAvailable($username)->count() == 0);
    }

    /**
     * Check if email available for registering.
     * Can be called for instant form validation.
     *
     * @param    string
     * @return    bool
     */
    public function isEmailAvailable($email)
    {
        return ((strlen($email) > 0) and User::isEmailAvailable($email)->count() == 0);
    }

    /**
     * Send email message of given type (activate, forgot_password, etc.)
     *
     * @param    string
     * @param    string
     * @param    array
     * @return    void
     */
    private function _sendEmail($type, $email, &$data)
    {
        $contactName = $data['username'];
        $contactEmail = $email;
        $emailType = $type;

        Mail::send(['emails.otentik.' . $type . '-html', 'emails.otentik.' . $type . '-txt'], $data, function ($message) use ($contactEmail, $contactName, $emailType) {
            $message->to($contactEmail, $contactName)
                ->subject(sprintf(str_replace('_', ' ', $emailType) . ' on %s', Config::get('otentikasi.website_name')))
                ->from('admin@gmail.com', 'Admin of ' . Config::get('otentikasi.website_name'));
            //->replyTo('admin@gmail.com',Config::get('otentikasi.website_name'));
        });
    }

    /**
     * Check the validation form of forgot password
     *
     * $param array
     * $return object
     */
    public function forgotPasswordValidation($dataforgotpassword)
    {
        $rules['login'] = 'required|email';
        $rules['g-recaptcha-response'] = 'required|captcha';

        /* run the validation rules on the inputs from the form */
        $validator = Validator::make($dataforgotpassword, $rules);

        /* this validation is from database perspective */
        $validator->after(function ($validator) use ($dataforgotpassword) {
            $this->forgotPasswordDbInvalid($validator, $dataforgotpassword);
        });

        return $validator;
    }

    /**
     * Forgot Password success on the site.
     * when validation process do not result error
     *
     * @param    array (username or email or both depending on settings in config file)
     * @return   string
     */
    public function forgotPasswordSuccess($dataforgotpassword)
    {
        $user = User::getUserByEmail($dataforgotpassword['login'])->first();

        $data = array('user_id' => $user->id, 'username' => $user->username, 'email' => $user->email, 'new_pass_key' => md5(rand() . microtime()));

        $user->new_password_key = $data ['new_pass_key'];
        $user->new_password_requested = date('Y-m-d H:i:s');
        $user->save();
        $data ['site_name'] = Config::get('otentikasi.website_name');

        // Send email with password activation link
        $this->_sendEmail('forgot_password', $data ['email'], $data);
        $message = Lang::get('otentikasi.email_forgot');

        return $message;
    }

    /**
     * Check if forgot Password invalid in DB perspective
     *
     * @param string
     * @return bool
     */
    private function forgotPasswordDbInvalid($validator, $dataforgotpassword)
    {
        /* check email apakah kosong, fungsi isEmailAvailable untuk memeriksa apakah email sudah
           ada dalam database atau tidak untuk keperluan registrasi.
           isEmailAvailable bernilai TRUE jika ternyata email tidak ada dalam database
        */
        if ($this->isEmailAvailable($dataforgotpassword['login'])) {
            $validator->errors()->add('login', Lang::get('otentikasi.not_available_email'));
        }

    }

    /**
     * Check if given password key is valid and user is authenticated.
     *
     * @param    string
     * @param    string
     * @return    bool
     */
    public static function canResetPassword($user_id, $new_pass_key)
    {
        $expire_period = Config::get('otentikasi.forgot_password_expire') == 0 ? 900 : Config::get('otentikasi.forgot_password_expire');
        $size_expired = time() - $expire_period;
        $time_expire_periode = Carbon\Carbon::createFromTimestamp($size_expired);

        if ((strlen($user_id) > 0) and (strlen($new_pass_key) > 0)) {
            if (User::canResetPassword($user_id, $new_pass_key, $time_expire_periode)->count() == 1) {
                return NULL;
            }
        }
        self::$error = array('forgot_expired' => Lang::get('otentikasi.forgot_expired'));
        return self::getErrorMessage();
    }

    /**
     * Check the validation form of forgot password
     *
     * $param array
     * $return object
     */
    public function resetPasswordValidation($dataresetpassword)
    {
        //$rules['email'] = 'required|email';
        $rules['password'] = 'required|confirmed|min:3';
        $rules['password_confirmation'] = 'required|min:3';

        /* run the validation rules on the inputs from the form */
        $validator = Validator::make($dataresetpassword, $rules);

        /* this validation is from database perspective */
        $validator->after(function ($validator) use ($dataresetpassword) {
            $this->resetPasswordDbInvalid($validator, $dataresetpassword);
        });

        return $validator;
    }

    /**
     * Check if reset Password invalid in DB perspective
     *
     * @param string
     * @return bool
     */
    private function resetPasswordDbInvalid($validator, $dataresetpassword)
    {
        $user_id = $dataresetpassword['user_id'];
        $new_pass_key = $dataresetpassword['token_key'];
        $new_password = $dataresetpassword['password'];

        if ((strlen($user_id) > 0) and (strlen($new_pass_key) > 0) and (strlen($new_password) > 0)) {
            $datauser = User::getUserById($user_id, TRUE);

            /* check apakah user itu ada */
            if (is_null($user = $datauser->first())) {
                $validator->errors()->add('forgot_expired', Lang::get('otentikasi.forgot_expired'));
            }

            /* check waktu reset password belum expired */
            $expire_period = Config::get('otentikasi.forgot_password_expire') == 0 ? 900 : Config::get('otentikasi.forgot_password_expire');
            $size_expired = time() - $expire_period;
            $time_expire_periode = Carbon\Carbon::createFromTimestamp($size_expired);
            if (User::canResetPassword($user_id, $new_pass_key, $time_expire_periode)->count() == 0) {
                $validator->errors()->add('forgot_expired', Lang::get('otentikasi.forgot_expired'));
            }

        }
    }

    /**
     * Reset Password success on the site.
     * when validation process do not result error
     *
     * @param    array (username or email or both depending on settings in config file)
     * @return   string
     */
    public function resetPasswordSuccess($dataresetpassword)
    {
        $user_id = $dataresetpassword['user_id'];
        $new_pass_key = $dataresetpassword['token_key'];
        $new_password = $dataresetpassword['password'];

        $hashed_password = Hash::make($new_password);

        $expire_period = Config::get('otentikasi.forgot_password_expire') == 0 ? 900 : Config::get('otentikasi.forgot_password_expire');
        $size_expired = time() - $expire_period;
        $time_expire_periode = Carbon\Carbon::createFromTimestamp($size_expired);

        if ($user = User::resetPassword($user_id, $hashed_password, $new_pass_key, $time_expire_periode)) { // success
            $data = array('user_id' => $user_id, 'username' => $user->username, 'email' => $user->email, 'new_password' => $new_password);
        }
        $data ['site_name'] = Config::get('otentikasi.website_name');

        /* Send email with new password */
        self::_sendEmail('reset_password', $data ['email'], $data);
        /* return message */
        $message = Lang::get('otentikasi.reset_success');
        return $message;
    }

    /**
     * Check the validation form of send again
     *
     * $param array
     * $return object
     */
    public function sendAgainValidation($datasendagain)
    {
        $rules['email'] = 'required|email';

        /* run the validation rules on the inputs from the form */
        $validator = Validator::make($datasendagain, $rules);

        /* this validation is from database perspective */
        $validator->after(function ($validator) use ($datasendagain) {
            $this->sendAgainDbInvalid($validator, $datasendagain);
        });

        return $validator;
    }

    /**
     * Check if send Again invalid in DB perspective
     *
     * @param string
     * @return bool
     */
    private function sendAgainDbInvalid($validator, $datasendagain)
    {
        if (strlen($datasendagain['email']) > 0 && (!$this->changeEmail($datasendagain['email']))) {
            $validator->errors()->add('email', Lang::get('otentikasi.email'));
        }

    }

    /**
     * Change email for activation and return some data about user:
     * user_id, username, email, new_email_key.
     * Can be called for not activated users only.
     *
     * @param $email
     * @return bool
     */
    private function changeEmail($email)
    {

        if (strtolower(Auth::user()->email) == strtolower($email)) { //email verifikasi sama
            return TRUE;
        } elseif ($this->isEmailAvailable($email)) {  //email verifikasi baru dan tidak ada dalam database
            return TRUE;
        } else {
            return FALSE;
        }
        return FALSE;

    }

    /**
     * Change username for activation and return some data about user:
     * user_id, username, email, new_email_key.
     * Can be called for not activated users only.
     *
     * @param $email
     * @return bool
     */
    private function changeUsername($username)
    {

        if (strtolower(Auth::user()->username) == strtolower($username)) { //email verifikasi sama
            return TRUE;
        } elseif ($this->isUsernameAvailable($username)) {  //email verifikasi baru dan tidak ada dalam database
            return TRUE;
        } else {
            return FALSE;
        }
        return FALSE;

    }

    /**
     * Send Again success on the site.
     * when validation process do not result error
     *
     * @param    array (username or email or both depending on settings in config file)
     * @return   string
     */
    public function sendAgainSuccess($datasendagain)
    {
        $user = Auth::user();
        $data = array('user_id' => $user->id, 'username' => $user->username, 'email' => $datasendagain['email']);

        if (strtolower(Auth::user()->email) == strtolower($datasendagain['email'])) { //email verifikasi sama
            $data ['new_email_key'] = $user->new_email_key;
        } else {  //email verifikasi baru dan tidak ada dalam database
            $data ['new_email_key'] = md5(rand() . microtime());
            User::setNewEmail($user->id, $datasendagain['email'], $data ['new_email_key'], FALSE);
        }

        /* send again verification code to email or new email */
        $data ['site_name'] = Config::get('otentikasi.website_name');
        $data ['activation_period'] = Config::get('otentikasi.email_activation_expire') / 3600;

        // Send email with password activation link
        $this->_sendEmail('activate', $data ['email'], $data);
        $message = Lang::get('otentikasi.activate_send_again');
        /* logout process for login agin */
        Auth::logout();
        return $message;
    }

    /**
     * Update Profile validation form of register
     *
     * $param array
     * $return object
     */
    public function updateProfileValidation($dataprofile)
    {
        /* Which rules to use to validation (based on config) */
        $rules['fullname'] = 'required';

        /* run the validation rules on the inputs from the form */
        $validator = Validator::make($dataprofile, $rules);

        return $validator;
    }

    /**
     * update profile success on the site.
     * when validation process do not result error
     *
     * @param    array (username or email or both depending on settings in config file)
     * @return   string
     */
    public function updateProfileSuccess($dataprofile)
    {
        $user = Auth::user();
        $user->userprofile->fullname = $dataprofile['fullname'];
        $user->userprofile->phone = $dataprofile['phone'];
        if ($dataprofile['day_of_birth'])
            $user->userprofile->day_of_birth = Carbon\Carbon::parse($dataprofile['day_of_birth']);
        //$user->userprofile->day_of_birth = Carbon\Carbon::createFromFormat('Y-m-d', $dataprofile['day_of_birth']);
        $user->userprofile->save();

        $message = Lang::get('otentikasi.update_userprofile_success');
        return $message;
    }

    /**
     * Update Account validation form of register
     *
     * $param array
     * $return object
     */
    public function updateAccountValidation($dataaccount)
    {
        /* Which rules to use to validation (based on config) */
        $rules['username'] = 'required|alpha_dash'.'|min:'. Config::get('otentikasi.username_min_length').'|max:'. Config::get('otentikasi.username_max_length');
        $rules['email'] = 'required|email';
        if (strlen($dataaccount['password']) > 0 || strlen($dataaccount['password_confirmation']) > 0) {
            $rules['password'] = 'required|confirmed'.'|min:'. Config::get('otentikasi.password_min_length').'|max:'. Config::get('otentikasi.password_max_length');
            $rules['password_confirmation'] = 'required'.'|min:'. Config::get('otentikasi.password_min_length').'|max:'. Config::get('otentikasi.password_max_length');
        }

        /* run the validation rules on the inputs from the form */
        $validator = Validator::make($dataaccount, $rules);

        /* this validation is from database perspective */
        $validator->after(function ($validator) use ($dataaccount) {
            $this->updateAccountDbInvalid($validator, $dataaccount);
        });

        return $validator;
    }

    /**
     * Check if update Account invalid in DB perspective
     *
     * @param string
     * @return bool
     */
    private function updateAccountDbInvalid($validator, $dataaccount)
    {
        /* user change his/her email */
        if (strtolower(Auth::user()->email) <> strtolower($dataaccount['email'])) { //check if user input same email
            if (strlen($dataaccount['email']) > 0 && (!$this->changeEmail($dataaccount['email']))) { //check if other user using new email
                $validator->errors()->add('email', Lang::get('otentikasi.email'));
            }
        }

        /* user change his/her username */
        if (strtolower(Auth::user()->username) <> strtolower($dataaccount['username'])) { //check if user input same username
            if (strlen($dataaccount['username']) > 0 && (!$this->changeUsername($dataaccount['username']))) { //check if other user using new username
                $validator->errors()->add('email', Lang::get('otentikasi.username'));
            }
        }

    }

    /**
     * update account success on the site.
     * when validation process do not result error
     *
     * @param    array (username or email or both depending on settings in config file)
     * @return   string
     */
    public function updateAccountSuccess($dataaccount)
    {
        $user = Auth::user();

        /* update data user account, not include email */
        $user->username = $dataaccount['username'];
        $user->password = Hash::make($dataaccount['password']);
        $user->last_ip = Request::getClientIp();
        $user->new_email = NULL;
        $user->new_email_key = NULL;
        $user->save();

        /* jika email mengalami perubahan maka lakukan prosedur ubah email */
        if (strtolower($user->email) <> strtolower($dataaccount['email'])) {

            $data = array('user_id' => $user->id, 'username' => $user->username, 'new_email' => $dataaccount['email']);
            $data ['site_name'] = Config::get('otentikasi.website_name');
            $data ['new_email_key'] = md5(rand() . microtime());

            User::setNewEmail($user->id, $dataaccount['email'], $data ['new_email_key'], TRUE);
            // Send email with password activation link
            $this->_sendEmail('change_email', $dataaccount['email'], $data);
            $message = Lang::get('otentikasi.change_user_email_success');
        } else {
            $message = Lang::get('otentikasi.update_userprofile_success');
        }

        unset($dataaccount ['password']); // Clear password (just for any case)
        return $message;
    }

    /**
     * Sengaja dibuat untuk testing aja
     *
     * @return string
     */
    public function process()
    {
        return 'oooo yeah Bunciono!!!';
    }

}