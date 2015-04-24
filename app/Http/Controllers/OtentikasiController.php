<?php namespace App\Http\Controllers;

/**
 * Description of KeamananController
 *
 * @author Dante
 */

use Illuminate\Http\Request;
use App\Bunciono\Facades\Otentikasi;

class OtentikasiController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('otentik', ['except' =>  ['logoutAction']
        ]);
    }

    /**
     * Dashboard page on the site
     *
     * @return void
     */
    public function dashboardShow()
    {
        return view('dashboard');
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logoutAction()
    {
        $message = Otentikasi::logout();
        return redirect('/otentik/login')->with('message', $message);
    }

    /**
     * Send activation email again, to the same or new email address
     *
     * @return void
     */
    public function sendAgainShow()
    {
        return view('otentik.send_again');
    }

    /**
     * Send activation email again, to the same or new email address
     *
     * @return void
     */
    public function sendAgainAction(Request $request)
    {
        $validator = Otentikasi::sendAgainValidation($request->all());

        if ($validator->fails()) {
            return redirect('/otentik/send_again')
                ->withErrors($validator->messages())// send back all errors to the login form
                ->withInput($request->only('email')); // send back the input (not the password) so that we can repopulate the form
        } else {
            /* successfull send again */
            $message = Otentikasi::sendAgainSuccess($request->all());
            return redirect('otentik/login')->with('message', $message);
        }
    }

    /**
     * Show the user profile.
     * After authentication success, user can show her/his profile.
     *
     * @return void
     */
    public function myProfileShow()
    {
        return view('otentik.myprofile');
    }

    /**
     * update user profile, to the same or new email address
     *
     * @return void
     */
    public function updateProfileAction(Request $request)
    {
        $validator = Otentikasi::updateProfileValidation($request->all());

        if ($validator->fails()) {
            return redirect('/otentik/myprofile')
                ->withErrors($validator->messages());// send back all errors to the login form
        } else {
            /* successfull update profile */
            $message = Otentikasi::updateProfileSuccess($request->all());
            return redirect('otentik/myprofile')->with(['message'=>$message, 'panel'=>'profile']);
        }
    }

    /**
     * update user account, to the same or new email address
     *
     * @return void
     */
    public function updateAccountAction(Request $request)
    {
        $validator = Otentikasi::updateAccountValidation($request->all());

        if ($validator->fails()) {
            return redirect('/otentik/myprofile')
                ->withErrors($validator->messages())// send back all errors to the login form
                ->withInput($request->except('password', 'password_confirmation'))
                ->withPanel('account'); // send back the input (not the password) so that we can repopulate the form
        } else {
            /* successfull update account */
            $message = Otentikasi::updateAccountSuccess($request->all());
            return redirect('otentik/myprofile')->with(['message'=>$message, 'panel'=>'account']);
        }
    }


}
