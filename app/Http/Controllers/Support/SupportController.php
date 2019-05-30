<?php

namespace App\Http\Controllers\Support;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Flash;
use DB;
use Recaptcha;
use App\Http\Controllers\Admin\AdminController as AdminController;
use App\Mail\SendIssues;
use App\Mail\SendRequestAccess;
use App\Mail\AdminNotifier;
use Mail;

class SupportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $admin = null;

    public function __construct()
    {
        /*$this->middleware('guest');*/
        $this->admin = new AdminController();
    }

    private function validator($data){
        return Validator::make($data, [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|max:250',
            'problem' => 'required|max:255',
            'g-recaptcha-response' => 'required|recaptcha',
        ]);
    }

    private function notifyAdmins($_request, $customer_name, $customer_email, $message = ''){
        $email = DB::table('Users')->select('Users.Id','Users.Email')->leftJoin('UserRole','Users.Id','UserRole.UserId')->where('UserRole.RoleId','=','1')->pluck('Users.Email')->all();
        $email[] = 'aarons.dbadmn@gmail.com';
        Mail::to($email)->send(new AdminNotifier($_request, $customer_name, $customer_email, $message));
    }

    private function sendUserAccess($email, $name){
        Mail::to($email)->send(new SendRequestAccess($name));
    }

    private function sendOtherIssues($email, $name){
        Mail::to($email)->send(new SendIssues($name));
    }

    public function supportMailer(Request $request){
        $data = $request->all();
        $full_name = $data['firstname'].' '.$data['lastname'];
        $_request = '';
        if($this->validator($data)->fails())
            return json_encode(['error'=>'Please completely and correctly answer the fields.']);
        if($data['problem'] == '1'){
            $this->sendUserAccess($data['email'], $full_name);
            $_request = 'Request access';
        }elseif($data['problem'] == '2'){
            $token = md5($data['firstname'].$data['lastname'].$data['email'].rand(1,9999));
            DB::table('Users')->where('Email','=',$data['email'])->update(['RequestPasswordToken'=>$token]);
            $link = route('password.request',[$token]);
            $this->admin->sendRequestChangePassword($data['email'], $full_name, $link);
            $_request = 'Password change (Forgot password)';
        }elseif($data['problem'] == '3'){
            $this->sendOtherIssues($data['email'], $full_name);
            $_request = 'issue';
        }
        $this->notifyAdmins($_request, $full_name, $data['email'], $data['message']);
        return json_encode(['result'=>'Your request has been sent.']);
    }

    public function getCaptcha(){
        return Recaptcha::render([ 'lang' => 'en' ]);
    }
}
?>