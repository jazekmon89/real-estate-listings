<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Flash;
use DB;
use Illuminate\Http\Request;
use Validator;
//use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    //use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function activateAccount(Request $request){
        $data = $request->all();
        $requestHash = $data['requestHash'];
        $validations = array (
            'password' => 'required|max:50|confirmed',
        );
        Validator::make($data, $validations)->validate();
        $password = md5($data['email'].$data['password']);
        DB::table('Users')
            ->where('RequestPasswordToken','=',$requestHash)
            ->update(['RequestPasswordToken'=>'', 'PasswordHash'=>$password, 'Activated'=>'1']);
        Flash::success('New password has been set. Your account is now activated.');
        return redirect('/?');
    }

    public function validatePasswordRequest($requestHash){
        $hash_flag = preg_match('/^[a-f0-9]{32}$/', $requestHash);
        if($hash_flag){
            $user = DB::table('Users')
                        ->where('RequestPasswordToken','=',$requestHash)
                        ->get()
                        ->all();
            if(count($user) > 0){
                $email = $user[0]->Email;
                return view('auth.requestPassword', compact('requestHash','email'));
            }
        }
        Flash::error(trans('passwordRequest.notExisting'));
        return redirect('/');
    }
}
