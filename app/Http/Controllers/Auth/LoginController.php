<?php

namespace App\Http\Controllers\Auth;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Flash;

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

    protected $username = 'email';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @param  string state
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateLogin(Request $request){
        $data = $request->all();
        return Validator::make($data, [
            'username' => 'required|max:255',
            'password' => 'required|max:255',
        ]);
    }


    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->all();
        $this->validateLogin($request);
        $remember_flag = array_key_exists('remember', $data) && $data['remember'] === 'on';
        if(Auth::attempt($data, $remember_flag, true)) {
            // Authentication passed...
            return redirect()->intended('/home');
        }
        Flash::error(trans('auth.failed'));
        return back();

    }
}
