<?php 

namespace App\Services;

use App\User;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class Registrar implements RegistrarContract {
	
	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'first_name' => 'required|max:100',
			'last_name' => 'required|max:100',
			'countrycode' => 'required|max:4',
			'contact_number' => 'required|numeric|digits_between:9,10|unique:user',
			//'contact_number' => 'required|numeric|digits:10|unique:user',
			'email' => 'required|email|max:255|unique:user',
			'password' => 'required|confirmed|min:6',
			'terms_conditions'=>'required',
		]);
	}
	
	public function loginvalidator(array $data)
	{
		return Validator::make($data, [
			'email' => 'required|max:255',
			'password' => 'required',
		], [
			'email.required'=>'The Mobile Number or Email ID field is required.',
		]);
	}

	public function addnewaddressvalidator(array $data)
	{
		return Validator::make($data, [			
			'first_address' => 'required',
			'city' => 'required',
			'state' => 'required',			
			'country' => 'required',
			'area' => 'required',				
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
		return User::create([
			'first_name' => trim(ucfirst($data['first_name'])),
			'last_name' => trim($data['last_name']),
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			'contact_number' => trim($data['contact_number']),			
			'countrycode' => $data['countrycode'], 
			'status'=>	0,
			'login_type'=>'register',
			'type'=> 0,
			//'type'=>$data['usertype'],
		]);
	}

}
