## Laravel 5 with Otentikasi Code (in english : Authentication)

Laravel 5 is one of best php framework. In my opinion, this framework do a lot of complex functional with simple approach.
Actually, before i learn laravel, i choose Codeigniter for doing my everyday work and that framework satisfy me a lot,
until they came with bad information, which that framework would not continued (deprecated).

Okey then... live must go on, i must switch fast to learn other framework, and continue to observation :

- Zend : to complex to me, i really confuse about their explanation, i am too stupid to learn this framework
- Symfony : good framework, but i feel 'rigid', i lost flexibility to explore my idea to the code.
- Laravel 4 : good framework, but i feel not right when you 'bind' everything in one time
- Laravel 5 : my choice that fit everything aspect i need.

Wow... but wait, how about everything i do in Codeigniter, such as Authentication Modul, oke then here we are
Laravel 5 with Otentikasi Code (Otentikasi is indonesian language for 'Authentication').

I use tank-auth when using authentication system in Codeigniter, On this occasion i appreciate and thank to
Ilya Konyukhov (http://konyukhov.com/soft/tank_auth/). This authentication system with laravel 5, basically use
tank-auth authentication for CI.

## Feature
* Create from Newbie to Every One :D, so the code easy to learn
* Create with Simple Methodology, Skinny Controller, Skinny Model, Fat Library (let complexity in one place) :D
* Using No Captcha from google
* Configuration flexibility in app/otentikasi.php
* Registration
* Activation
* Login with attempt system
* Forgot Password
* Reset Password
* Change profile
* Change username and email with mechanism or procedure
* etc, you can try it and enjoy

## Installation

First, after you download this code, go to the root application :
will install :
* laravel main code
* debugger from barryvdh/laravel-debugbar
* captcha from anhskohbo/no-captcha
```sh
$ composer update
```

Second, i use gmail for testing sending email notification, please prepare your gmail account, then create .env file in your root application  :
```sh
APP_ENV=local
APP_DEBUG=true
APP_KEY= --create this with php artisan command--

DB_HOST=localhost
DB_DATABASE= --your database--
DB_USERNAME= --your user database--
DB_PASSWORD= --your password database--

MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_FROM_ADDRESS= --your admin mail--
MAIL_FROM_NAME= --your name--
MAIL_ENCRYPTION=ssl
MAIL_USERNAME= --your user gmail--
MAIL_PASSWORD= --your password gmail--

CACHE_DRIVER=file
SESSION_DRIVER=file

NOCAPTCHA_SECRET= --your secret code from no captcha--
NOCAPTCHA_SITEKEY= --your site key from no captcha--
```

Third, Do migration and seed
```sh
$ php artisan migrate
$ php artisan db:seed
```

Fourth, test the application with :
* Open http://localhost/otentik
* Default login : 'admin@your-site.com' with password : 'admin123'

## Contributing

Thank you for considering contributing to this system

### License

Karepmoe Licensed
