<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\Timing,App\Restaurent;
use Illuminate\Contracts\Auth\Registrar;
use \Exception;
use Validator;
use Form;
use Illuminate\Pagination\Paginator;

class TimingController extends \App\Http\Controllers\Controller {

	/*
	|--------------------------------------------------------------------------
	| Timing Controller
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
	public function index($restaurantid)
	{

		$data = [];

		try {
			if(Session::get('access.role') == 'restaurant') {
				$restaurant = Restaurent::whereIn('id', Session::get('access.restaurant_ids'))->findOrFail($restaurantid);
			} else {
				$restaurant = Restaurent::findOrFail($restaurantid);
			}

			if(!$restaurant) throw new Exception("No record was found.", 404);

			$data['data'] = Timing::lang()->select('timing.*','restaurant.name as restaurant_name')
				->Join('restaurant', 'restaurant.id', '=', 'timing.restaurant_id')
				->where('restaurant_id', $restaurantid)->orderBy('weekday','ASC')->get();		
			return view('admin.timing.index',['data' => $data,'restaurant' => $restaurant]);

		} catch (\Exception $e) {
            return redirect()->back();
        }
	}


	public function add($restaurant_id,Request $request) {

		try {
			if(Session::get('access.role') == 'restaurant') {
				$restaurant = Restaurent::whereIn('id', Session::get('access.restaurant_ids'))->findOrFail($restaurant_id);
			} else {
				$restaurant = Restaurent::findOrFail($restaurant_id);
			}

			if(!$restaurant) throw new \Exception("No record was found.", 404);

			if($request->isMethod('post')) {
				$input = $request->all();
			   	$this->validate($request,[
			        'open' => 'required',
			        'closing' => 'required',
			        'weekday' => 'required|unique:timing,weekday,null,id,restaurant_id,'.$restaurant_id,		        
			        'delivery_start' => 'required',
			        'delivery_end' => 'required'
			    ]); 	

			    Timing::create($input);					    
			    Session::flash('flash_message', trans('admin.successfully.added')); 
			    return redirect()->back();	        
			}
			return view('admin.timing.add',['restaurant'=>$restaurant,'restaurant_id'=>$restaurant_id]);

		} catch (\Exception $e) {
            return redirect()->back();
        }
	}


	public function edit($id) {

		try {
			if(Session::get('access.role') == 'restaurant') {
				$timing = Timing::whereIn('restaurant_id', Session::get('access.restaurant_ids'))->findOrFail($id);
			} else {
				$timing = Timing::findOrFail($id);
			}

			if(!$timing) throw new Exception("No record was found.", 404);

	    	$restaurant = Restaurent::findOrFail($timing->restaurant_id);

	    	return view('admin.timing.edit',['restaurant'=>$restaurant,'timing'=>$timing,'restaurant_id'=>$timing->restaurant_id]);

    	} catch (\Exception $e) {
            return redirect()->back();
        }
	}


	public function update($id, Request $request)
	{
	    $timing = Timing::findOrFail($id);

	   	$this->validate($request,[
	        'open' => 'required',
	        'closing' => 'required',
	        'weekday' => 'required|unique:timing,weekday,'.$id.',id,restaurant_id,'.$timing->restaurant_id,
	        'delivery_start' => 'required',
	        'delivery_end' => 'required'
	    ]);
	    
	    $input = $request->all();
	    $timing->fill($input)->save();
		
	    Session::flash('flash_message', trans('admin.successfully.updated'));

	    return redirect()->back();
	}

	public function delete($id, Request $request) {
		$timing = Timing::find($id);
		$restaurant_id = $timing->restaurant_id;
		$timing->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/timing'.'/'.$restaurant_id);
	}

}