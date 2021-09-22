<?php namespace App\Services;

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
			'country_code' => 'required|max:4',
			'contact_number' => 'required|max:20',
			'email' => 'required|email|max:255|unique:user',
			'password' => 'required|confirmed|min:6',
			'terms_conditions'=>'required',
		]);
	}
	public function loginvalidator(array $data)
	{
		return Validator::make($data, [
			'email' => 'required|email|max:255',
			'password' => 'required',
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
			'first_name' => $data['first_name'],
			'last_name' => $data['last_name'],
			'contact_number' => $data['country_code']. "-". $data['contact_number'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			'status'=>	0,
			'login_type'=>'register',
			//'type'=>$data['usertype'],
			'type'=>0,
		]);
	}
}
