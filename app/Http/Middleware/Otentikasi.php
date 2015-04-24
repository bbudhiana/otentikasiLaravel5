<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Otentikasi
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('otentik/login');
            }
        } /* jika ternyata user sudah terdaftar namun belum aktivasi maka lempar ke proses aktivasi */
        else {
            if ($this->auth->check()) {
                if (!$this->auth->user()->activated == 1) {
                    if ($request->route()->getPath()<>"otentik/send_again")
                        return redirect('otentik/send_again');
                } else {
                    if ($request->route()->getPath()=="otentik/send_again")
                        return redirect()->back();
                }
            }
        }


        return $next($request);
    }

}
