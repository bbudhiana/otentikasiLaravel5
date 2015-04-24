<?php namespace App\Http\Controllers\Otentik;

use App\Http\Controllers\Controller;
use App\Bunciono\Facades\Otentikasi;
use Illuminate\Http\Request;

class OtentikController extends Controller {


	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('tamu');
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	public function loginShow(Request $request)
	{
		/* Get login for counting attempts to login */
		$login_attempt = filter_var($request->input('login'), FILTER_SANITIZE_STRING);
		if (Otentikasi::isMaxLoginAttemptsExceeded($login_attempt)) {
			/*return View::make('auth.login', array('is_need_captcha' => TRUE));*/
			return view('otentik.login', array('is_need_captcha' => TRUE));
		}

		return view('otentik.login')->with('is_need_captcha', FALSE);
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	public function loginAction(Request $request)
	{
		$validator = Otentikasi::loginValidation($request->all());

		/* if the validator fails, redirect back to the form */
		if ($validator->fails()) {
			return redirect('/otentik/login')
				->withErrors($validator)// send back all errors to the login form
				/*
                 >withErrors($validator->errors()) //bisa seperti ini
                ->withErrors($validator->messages()) //bisa juga seperti ini
                */
				->withInput($request->only('login')); // send back the input (not the password) so that we can repopulate the form
		} else {
			/* successfull login */
			$message = Otentikasi::loginSuccess($request->all());
			return redirect()->intended('/otentik/dashboard')->with('message', $message);

			/* check data login on database */
			/*$error_messages = Otentikasi::login(Request::all());
            if (!($error_messages->all())) {
                return redirect()->intended('/otentik/dashboard');
            } else {
                return redirect('/otentik/login')
                    ->withErrors($error_messages)// send back all errors to the login form
                    ->withInput(Request::only('login')); // send back the input (not the password) so that we can repopulate the form
            }*/
		}
	}


	/**
	 * Register user on the site
	 *
	 * @return void
	 */
	public function registerShow()
	{
		return view('otentik.register', array('is_need_captcha' => Otentikasi::isRegisterNeedCaptcha()));
	}

	/**
	 * Register user on the site
	 *
	 * @return void
	 */
	public function registerAction(Request $request)
	{
		$validator = Otentikasi::registerValidation($request->all());

		/* if the validator fails, redirect back to the form */
		if ($validator->fails()) {
			return redirect('/otentik/register')
				->withErrors($validator->errors())// send back all errors to the login form
				->withInput($request->except('password', 'password_confirmation', 'captcha')); // send back the input (not the password) so that we can repopulate the form
		} else {
			/* successfull register */
			$message = Otentikasi::registerSuccess($request->all());
			return redirect('/otentik/login')->with('message', $message);
		}
	}

	/**
	 * Generate reset code (to change password) and send it to user
	 *
	 * @return void
	 */
	public function forgotPasswordShow()
	{
		return view('otentik.forgot_password');
	}

	/**
	 * Generate reset code (to change password) and send it to user
	 *
	 * @return void
	 */
	public function forgotPasswordAction(Request $request)
	{
		$validator = Otentikasi::forgotPasswordValidation($request->all());

		if ($validator->fails()) {
			return redirect('/otentik/forgot_password')
				->withErrors($validator->messages())// send back all errors to the login form
				->withInput($request->only('login')); // send back the input (not the password) so that we can repopulate the form
		} else {
			/* successfull forgot password */
			$message = Otentikasi::forgotPasswordSuccess($request->all());
			return redirect('otentik/login')->with('message', $message);
		}
	}

	/**
	 * Replace user email with a new one.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	public function resetPasswordShow(Request $request)
	{
		$user_id = $request->route('user_id');
		$new_pass_key = $request->route('key');

		/* if max reset time then not allow to continue or must re-reset procedure */
		if (!is_null($errors = Otentikasi::canResetPassword($user_id, $new_pass_key))) {
			//$errors = 'Your activation key is incorrect or expired. Please repeat the forgot password process.';
			return redirect('otentik/login')->withErrors($errors);
		}
		return view('otentik.reset_password', ['token_key' => $new_pass_key, 'user_id' => $user_id]);
	}

	/**
	 * Replace user email with a new one.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	public function resetPasswordAction(Request $request)
	{

		$validator = Otentikasi::resetPasswordValidation($request->all());

		if ($validator->fails()) {
			return redirect('/otentik/reset_password/' . $request->input('user_id') . '/' . $request->input('token_key'))
				->withErrors($validator->messages());// send back all errors to the login form
			//->withInput(Request::only('email')); // send back the input (not the password) so that we can repopulate the form
		} else {
			/* successfull reset password */
			$message = Otentikasi::resetPasswordSuccess($request->all());
			return redirect('otentik/login')->with('message', $message);
		}
	}

	/**
	 * Activate user account.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	public function activateAction(Request $request)
	{
		$user_id = $request->route('user_id');
		$new_email_key = $request->route('key');

		// Activate user
		if (is_null($response = Otentikasi::activateUser($user_id, $new_email_key))) { // success
			Otentikasi::logout();

			$message = 'Your account has been successfully activated.';

			return redirect('otentik/login')->with('message', $message);
		} else { // fail
			return redirect('otentik/login')
				->withErrors($response); // send back all errors to the login form
		}
	}

}
