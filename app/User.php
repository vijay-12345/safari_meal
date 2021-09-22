<?php 

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Registrar;

class User extends AppModel implements AuthenticatableContract, CanResetPasswordContract 
{
	use Authenticatable, CanResetPassword;

	use SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';
	

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable = ['profile_image','role_id','first_name','last_name','contact_number', 'email', 'password','status', 'login_type','company','newsletter','countrycode','remember_token', 'last_lat', 'last_long'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

    protected $dates = ['deleted_at'];
    /**
     * Get the address for the user.
     */
    public function address()
    {
        return $this->hasMany('App\UserAddress');
    }

    public function userAddressDetail(){
        return $this->hasMany('App\UserAddress', 'user_id', 'id')
        	->leftJoin('country', 'user_address.country_id', '=', 'country.country_code')
			->leftJoin('state', 'user_address.state_id', '=', 'state.state_id')
			->leftJoin('city', 'user_address.id', '=', 'city.id')
			->leftJoin('area', 'user_address.area_id', '=', 'area.id')
			->select('user_address.*','user_address.id as user_addressid','country.*','state.*','city.*','area.*')
			->where('user_address.country_id', '<>', 0)
			->get();
    }

    public function ajaxUserAddressDetail($optAddressId){
    	$result = '';
       	$address = $this->hasMany('App\UserAddress', 'user_id', 'id')
        	->where('user_address.id',$optAddressId)
        	->leftJoin('country', 'user_address.country_id', '=', 'country.country_id')
			->leftJoin('state', 'user_address.state_id', '=', 'state.state_id')
			->leftJoin('city', 'user_address.id', '=', 'city.id')
			->leftJoin('area', 'user_address.area_id', '=', 'area.id')
			->leftJoin('user', 'user_address.user_id', '=', 'user.id')
			->select('user.*','user_address.*','user_address.id as user_addressid','country.*','state.*','city.*','area.*')
			->first();			
		$result ='<table class="table">
					<tbody>
						<tr><td>'. 
							$address->first_name.'  '.$address->last_name.','. 
						'</td></tr>
						<tr><td>  
							 '.$address->first_address.', '.  $address->second_address.
						'</td></tr>						
						<tr><td> 
							'. $address->name.', '. $address->state_name.' ,'.$address->zip.' ,'.$address->country_name.'
						</td></tr>
						<tr><td> 
						Phone +'.$address->countrycode.'-  '.$address->contact_number.
						'</td></tr>						
					</tbody>
				</table>';
		return $result;
    }

	// rule for api
	public static $rulesApi = array(
		'email' => 'required|email|unique:user,email',
		'password' => 'required|min:6|max:20',
		'contact_number' => 'required|numeric|digits_between:9,10',
		'first_name' => 'required',
		'last_name' => 'required',
		'countrycode' => 'required'
	);

	public static $registerRulesApi = array(
		'email' => 'required|email|unique:user,email',
		'password' => 'required|min:6|max:20',
		'contact_number' => 'required|numeric|digits_between:9,10',
		'first_name' => 'required',
		'last_name' => 'required',
		'countrycode' => 'required',
		'otp' => 'required|numeric',
	);

	public static $socialApiRules = array(
		'email' => 'required|email|unique:email',		
		'first_name' => 'required',
		'last_name' => 'required'	
	);

	public static function rulesUpdateApi( $UserId )
	{
		return array(
			'email' => 'email|unique:user,email,'.$UserId.',id'
		);
	}

	// end rule for api	
	public $rulesWeb = array(
		'email' => 'required|email|unique:user,email',
		'password' => 'required|min:5|max:20',
		'contact_number' => 'required|numeric|digits_between:9,10',		
		'first_name' => 'required',
		'last_name' => 'required',		
	);

	public static function rulesUpdateWeb( $UserId )
	{
		return array(
			'email' => 'required|email|unique:user,email,'.$UserId.',id',			
			'first_name' => 'required',
			'contact_number' => 'required|numeric|digits_between:9,10',		
			'last_name' => 'required',
		);
	}

	public static function rulesUpdatePassword( $UserId )
	{	
		\Validator::extend('old_password', function ($attribute, $value, $parameters, $validator){

			return \Hash::check($value, \Auth::user()->password);
		});
		return array(
			'old_password'                  => 'required|old_password',
			'new_password'                  => 'required|min:5|max:20|confirmed|different:old_password',
			'new_password_confirmation'     => 'required|min:5|max:20|different:old_password|same:new_password'		
		);
	}

	public static function rulesAddnewAddress(){		
		return array(
			'first_address' => 'required'	
		);			
	}

	public static function finduser($data)
	{

		if( isset( $data['email'] ) )
		{
			if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $data['email'])) {
				$user = User::where('contact_number', trim($data['email']))->first();
			} else {
				$user = User::where('email', trim($data['email']))->first();
			}
			if( $user ) {
				return $user;
			}
		}

		if( isset( $data['id'] ) )
		{
			$user = User::where('email', $data['id'])->first();
			if( $user ) {
				return $user;
			}
		}

		if( isset( $data['remember_token'] ) )
		{
			$user = User::where('remember_token', $data['remember_token'])->first();
			if( $user ) {
				return $user;
			}
		}

		return '';
	}

}