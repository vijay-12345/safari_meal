<?php

namespace App\Http\Controllers\Admin;
use Auth, Session, DB, Validator;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use \Form;
use App\Setting;
use App\Country;

class SettingController extends \App\Http\Controllers\Controller {

	/*
	|--------------------------------------------------------------------------
	| Coupon Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		if(Session::get('access.role') != 'admin') return redirect('/'); 
		
		$model = Setting::where('id', 1);

        $model = $model->first();

        $countries = Country::lists('country_name','country_id');

        $timezones = \Config::get('timezones');

        ksort($timezones);

        if($request->isMethod('post'))
        {
        	$arguments = $request->all();

            $validator = Validator::make($arguments, [
				'country_id' => 'required',
				'country_code' => 'required|integer',
				'timezone' => 'required',
				'radius' => 'required|numeric',
			], [
				'country_id.required'=>'The Country field is required.',
				'country_code.required'=>'The Country Code field is required.',
			]);

            if ( $validator->fails() ) return redirect()->back()->withErrors($validator)->withInput();

            if($model) $model->fill($arguments)->save();

            else Setting::create($arguments);

            \Session::flash('flash_message', 'Seting parameters saved successfully.');

            return redirect($request->segment(2).'/setting');
        }

        return view('admin.setting.index')->with([
        				'model'     => $model,
        				'countries' => $countries,
        				'timezones' => $timezones
        			]);
	}

}