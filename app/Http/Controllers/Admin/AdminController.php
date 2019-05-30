<?php

namespace App\Http\Controllers\Admin;

use DB;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flash;
use App\User;
use Yajra\Datatables\Facades\Datatables;
use Mail;
use App\Mail\PasswordReqeustNotification;
use App\Mail\ChangePassword;

class AdminController extends Controller
{

    protected $user = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User;
        $this->middleware('adminaccess');
    }

    protected function validateLogin(Request $request){
        $data = $request->all();
        return Validator::make($data, [
            'username' => 'required|max:255',
            'password' => 'required|max:255',
        ]);
    }

    public function index(Request $request)
    {
        return view('Admin.Users.index');
    }

    public function showUsersPage(){
        $roles = DB::table('Role')->get()->all();
        return view('Admin.Users.index', compact('roles'));
    }

    public function getUsers(){
        //$users = DB::table('User')->where('Activated', '1');
        $users = $this->user->sp_GetAllUser();
        return Datatables::of($users)->make(true);
    }

    public function registerUser(Request $request){
        $data = $request->all();
        $res = $this->user->sp_User_iu_first([
            null,
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            //$data['password'],
            null, null, 0, null, $data['role']
        ]);
        if($res !== null)
            return 'true';
        return 'false';
    }

    public function editUser(Request $request) {
        $data = $request->all();
        $res = $this->user->sp_User_iu_first([
            $data['id'],
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            null, null, $data['activated'], null,
            $data['role']
        ]);

        if(is_object($res) && property_exists($res, 'result'))
            return 'true';
        else if(is_object($res) && property_exists($res, 'error'))
            return $res->error;
        return 'false';
    }

    public function deleteUser(Request $request) {
        $data = $request->all();
        $res = $this->user->sp_DeleteUser([$data['id']]);
        /*$res = $this->user->sp_User_iu([
            $data['id'],
            null,
            null,
            null,
            null, null, '0', null
        ]);*/

        if($res !== null)
            return 'true';
        return 'false';
    }

    public function sendPasswordRequest($email, $name, $link){
        Mail::to($email)->send(new PasswordReqeustNotification($name, $link));
    }

    public function sendRequestChangePassword($email, $name, $link){
        Mail::to($email)->send(new ChangePassword($name, $link));
    }

    public function requestPassword(Request $request){
        $data = $request->all();
        $UserId = $data['UserId'];
        $res = DB::table('Users')->where('Id','=',$UserId)->get()->first();
        if($res){
            $token = md5($res->FirstName.$res->LastName.$res->Email.rand(1,9999));
            DB::table('Users')->where('Id','=',$UserId)->update(['RequestPasswordToken'=>$token]);
            $link = route('password.request',[$token]);
            $this->sendRequestChangePassword($res->Email, $res->FirstName, $link);
            return 'true';
        }
        return 'false';
    }

    public function addUser(Request $request) {
        $data = $request->all();
        $token = md5($data['firstname'].$data['lastname'].$data['email'].rand(1,9999));
        $res = $this->user->sp_User_iu_first([
            null,
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            null, null,
            '0',//$data['activated'],
            $token, intval($data['role'])
        ]);
        if($res !== null){
            $link = route('password.request',[$token]);
            $this->sendPasswordRequest($data['email'], $data['firstname'], $link);
            return 'true';
        }
        return 'false';
    }

}
