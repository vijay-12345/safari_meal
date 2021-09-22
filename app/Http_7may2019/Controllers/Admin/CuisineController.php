<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\Restaurent;
use App\Cuisine;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Form;
use Illuminate\Pagination\Paginator;

class CuisineController extends \App\Http\Controllers\Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
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
		//clear session first time	
		if ($request->isMethod('get') && count($request->all()) == 0){	
			Session::forget('filter');			
		}	
		/* filter 			
		*/	
		if($request->has('sorting')){
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		}else{
			$filter = ['sort'=>'asc','field'=>'name'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}
		//print_r($filter);die;
		$data = [];		
		$q = Cuisine::lang();	
		$q->orderBy($filter['field'], $filter['sort']);		
		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('name','like', '%'.session('filter.search').'%');
		
		}
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//echo "<pre>";
		//print_r($data);die;
		//dd(DB::getQueryLog());
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}
		//$data['restaurants']  = Restaurent::restaurant()->select('id','name')->get();

		return view('admin.cuisine.index',['data'=>$data,'filter'=>$filter]);
		//echo "testig..";die;
	}
	public function add(Request $request){

		if($request->isMethod('post')) {
		    $this->validate($request, [
		        'name' => 'required'
		    ]);
		    $input = $request->all();
		    Cuisine::create($input);
		    Session::flash('flash_message',  trans('admin.successfully.added'));
		    //return redirect('/admin/customer');	
		    return redirect()->back();	        
	        
		}
		return view('admin.cuisine.add');
	}
	public function edit($id){
    	$cuisine = Cuisine::findOrFail($id);
    	//print_r($cuisine);die;
    	return view('admin.cuisine.edit')->withCuisine($cuisine);
		
	}
	public function update($id, Request $request)
	{
		//echo "testing..";die;
	    $cuisine = Cuisine::findOrFail($id);

	    $this->validate($request, [
	        'name' => 'required'	       
	    ]);

	    $input = $request->all();
	    //echo "<pre>";
	    //print_r($input);die;
	    $cuisine->fill($input)->save();

	    Session::flash('flash_message',  trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	public function delete($id){
		//echo "testing...";die;
		$user = Cuisine::find($id);

		$user->delete();		
		Session::flash('flash_message',  trans('admin.successfully.deleted'));
		return redirect('admin/cuisine');
	}	

}