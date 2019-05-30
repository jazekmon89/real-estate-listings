<?php

namespace App;

use DB;
use App\StoredProcTrait as StoredProc;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Flash;

class User extends Authenticatable
{
    use Notifiable, StoredProc;

    protected $_is_agent = null;

    protected $_is_admin = null;

    protected $views_prefix = null;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'roleid', 'username', 'activated'
    ];

    public $appends = ['name', 'is_agent', 'is_admin', 'views_prefix'];

    public $casts = [
        'id' => 'string'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function findByProviderAndEmail($provider=null, $email=null) {
        $email = $this->email;
        $password = $this->password;
        
        $auth = $this->sp_ValidateUserAndRole_first([$email, $password]);
        if(!isset($auth->{'0'}) && $auth->Activated)
            $this->attachUserDetails($auth, $password);
        elseif(property_exists($auth, 'Activated') && $auth->Activated)
            Flash::error(trans('auth.disabled'));
        else
            Flash::error(trans('auth.failed'));
        return $this;
    }

    private function attachUserDetails($data, $password){
        $this->firstname = $data->FirstName;
        $this->lastname = $data->LastName;
        $this->email = $data->UserEmail;
        $this->roleid = $data->RoleId;
        $this->id = $data->UserId;
        $this->password = '';
        $this->views_prefix = $this->getIsAdminAttribute() ? 'Admin.' : 'Agent.';
        $this->activated = $data->Activated;
        //$this->password = md5($this->email.$password);
    }

    public function getNameAttribute() {
        $name = [$this->firstname, $this->lastname];

        if (empty($name)) $name = [$this->email];
        return implode(' ', $name);
    }   

    public function getAuthIdentifier() {
        return $this->id;
    }

    public function getIsAgentAttribute() {
        if ($this->_is_agent !== null)
            return $this->_is_agent;
        return $this->_is_agent = $this->roleid == 2;
    }

    public function getIsAdminAttribute() {
        if ($this->_is_admin !== null)
            return $this->_is_admin;
        return $this->_is_admin = $this->roleid == 1;
    }
}
