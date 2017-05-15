<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use DB;
use Mail;
use Session;
use App\User;
use App\Mail\EmailVerification;
use \Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:191|unique:users',
            'username' => 'required|string|alpha_dash|max:191|min:6|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => strtolower($data['username']),
            'password' => bcrypt($data['password']),
            'verification_token' => strtolower(str_random(rand(50, 90))),
            'verification_token_time' => Carbon::now()->addHours(2),
        ]);
    }

    public function Register(Request $request)
    {
        // dd($request->all());
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        DB::beginTransaction();

        try {
            $user = $this->create($request->all());

            $email = new EmailVerification($user);
            Mail::to($user->email)->send($email);
            Session::flash('success', 'You have been registered successfully.');
            Session::flash('info', 'Please check your email to verify your email address.');

            DB::commit();
            return back();
        } catch (Exception $e) {
            DB::rollback();
            return back();
        }
    }

    public function verify($token)
    {
        $user = User::where('verification_token', $token)->first();
        if(!$user) {
            Session::flash('error', 'Token is invalid. Please register and click on the link that will be sent to you.');
            return redirect('login');
        } elseif($user->verification_token_time->isPast()) {
          $user->verification_token = strtolower(str_random(rand(50, 90)));
          $user->verification_token_time = Carbon::now()->addHours(2);
          $user->save();
          $email = new EmailVerification($user);
          Mail::to($user->email)->send($email);
          Session::flash('warning', 'Token is expired. We has just sent another verification email to you. Please check your inbox.');
          return redirect('login');
        }
        $user->verify();
        Session::flash('success', 'Success! Your email is verified. You can login now.');
        return redirect('login');
    }
}
