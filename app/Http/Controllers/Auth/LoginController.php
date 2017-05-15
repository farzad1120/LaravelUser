<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // public function login(Request $request)
    // {
    //   $this->validateLogin($request);
    //
    //     if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //         return redirect()->intended('/home');
    //     } elseif (Auth::attempt(['username' => $request->email, 'password' => $request->password])) {
    //         return redirect()->intended('/home');
    //     }
    //   return $this->sendFailedLoginResponse($request);
    // }
    //


        /**
         * Validate the user login request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return void
         */
        protected function validateLogin(Request $request)
        {
            $messages = [
                $this->username() . '.required' => 'We need to know your username or e-mail address.',
            ];
            $this->validate($request, [
                $this->username() => 'required|string',
                'password' => 'required|string',
            ], $messages);
        }


    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $login = $this->guard()->attempt($this->credentials($request), $request->has('remember'));
        if (!$login) {
            $credentials = [
            'username' => $request->email,
            'password' => $request->password
        ];
            $login = $this->guard()->attempt($credentials, $request->has('remember'));
        }
        return $login;
    }


        /**
         * The user has been authenticated.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  mixed  $user
         * @return mixed
         */
        protected function authenticated(Request $request, $user)
        {
            //
        }
}
