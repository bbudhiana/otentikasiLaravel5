<?php

return [
    /*
        |--------------------------------------------------------------------------
        | Website properti
        |--------------------------------------------------------------------------
        |
        | Berikut ini adalah konfigurasi dasar dari website yang menggunakan
        | modul keamanan, yaitu nama website dengan variabel 'website_name' dan
        | email website yaitu 'website_email'.
        | Cara mengaksesnya dalam skrip Laravel : Config::get('keamanan.website_name');
        | atau dengan helper : $value = config('keamanan.website_name');
        |
	*/
    'website_name' => 'your-site.com',
    'website_email' => 'your-email@your-site.com',

    /*
        |--------------------------------------------------------------------------
        | Registration settings
        |--------------------------------------------------------------------------
        |
        | 'allow_registration' = Registration is enabled or not
        | 'captcha_registration' = Registration uses CAPTCHA
        | 'email_activation' = Requires user to activate their account using email after registration.
        | 'email_activation_expire' = Time before users who don't activate their account getting deleted from database. Default is 48 hours (60*60*24*2).
        | 'email_account_details' = Email with account details is sent after registration (only when 'email_activation' is FALSE).
        | 'use_username' = Username is required or not.
        |
        | 'username_min_length' = Min length of user's username.
        | 'username_max_length' = Max length of user's username.
        | 'password_min_length' = Min length of user's password.
        | 'password_max_length' = Max length of user's password.
        |--------------------------------------------------------------------------
    */
    'allow_registration' => TRUE,
    'captcha_registration' => TRUE,
    'email_activation' => TRUE,
    'email_activation_expire' => 60 * 60 * 24 * 2,
    'email_account_details' => TRUE,
    'use_username' => FALSE,

    'username_min_length' => 4,
    'username_max_length' => 25,
    'password_min_length' => 4,
    'password_max_length' => 25,

    /*
        |--------------------------------------------------------------------------
        | Login settings
        |--------------------------------------------------------------------------
        |
        | 'login_by_username' = Username can be used to login.
        | 'login_by_email' = Email can be used to login.
        | You have to set at least one of 2 settings above to TRUE.
        | 'login_by_username' makes sense only when 'use_username' is TRUE.
        |
        | 'login_record_ip' = Save in database user IP address on user login.
        | 'login_record_time' = Save in database current time on user login.
        |
        | 'login_count_attempts' = Count failed login attempts.
        | 'login_max_attempts' = Number of failed login attempts before CAPTCHA will be shown.
        | 'login_attempt_expire' = Time to live for every attempt to login. Default is 24 hours (60*60*24).
        |--------------------------------------------------------------------------
    */
    'login_by_username' => FALSE,
    'login_by_email' => TRUE,
    'login_record_ip' => TRUE,
    'login_record_time' => TRUE,
    'login_count_attempts' => TRUE,
    'login_max_attempts' => 5,
    'login_attempt_expire' => 60 * 60 * 24,

    /*
        |--------------------------------------------------------------------------
        | Forgot password settings
        |--------------------------------------------------------------------------
        |
        | 'forgot_password_expire' = Time before forgot password key become invalid. Default is 15 minutes (60*15).
        |--------------------------------------------------------------------------
    */
    'forgot_password_expire' => 60 * 15,

];
