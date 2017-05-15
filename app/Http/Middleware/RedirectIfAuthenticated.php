<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use \Session;
use \Carbon\Carbon;
use App\User;
use App\Mail\EmailVerification;
use Mail;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/home');
        }

        $response = $next($request);
        if (Auth::user() && !Auth::user()->isVerified()) {
            if (Auth::user()->verification_token_time->isPast()) {
                Auth::user()->verification_token = strtolower(str_random(rand(50, 90)));
                Auth::user()->verification_token_time = Carbon::now()->addHours(2);
                $email = new EmailVerification(Auth::user());
                Mail::to(Auth::user()->email)->send($email);
                $error = 'Your email has not been verified yet, and Your previous verification token has been expired; therefore, another verification email has just been sent to you. Please check your inbox.';
            } else {
                $error = 'Your email has not been verified yet. Please check your inbox and click on the link in the verification email.';
            }
            Auth::logout();
            return redirect('login')->withInput()->with(compact('error'));
        }
        return $response;
    }
}
