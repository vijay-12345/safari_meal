<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\Restaurent;
use App\Review;
use Illuminate\Contracts\Auth\Registrar;
use \Mail;
use \Exception;
use Form;
use Illuminate\Pagination\Paginator;

class ReviewController extends \App\Http\Controllers\Controller {

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
		//clear session first time	
		if ($request->isMethod('get') && count($request->all()) == 0){	
			Session::forget('filter');			
		}	
		/* filter 			
		*/		
		if($request->has('sorting')){
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		}else{
			$filter = ['sort'=>'desc','field'=>'date'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
			//handle when blank/space inpute in search
			if($request->input('search'))
				if(session('filter.search')==null)
				{
					$data = null;
					return view('admin.review.index',['data'=>$data,'filter'=>$filter]);
				}
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}
		if($request->has('restaurant_id')){
			session(['filter.restaurant_id' => $request->input('restaurant_id')]);
		}elseif(!Session::has('filter.restaurant_id')){
			session(['filter.restaurant_id' => 'All']);
		}
		if($request->has('rating')){
			session(['filter.rating' => $request->input('rating')]);
		}elseif(!Session::has('filter.rating')){
			session(['filter.rating' => 'All']);
		}		
		//print_r($filter);die;
		$data = [];		
		$q = Review::select('review.*','restaurant.name as restaurant_name','restaurant.is_home_cooked','user.first_name as user_fname','user.last_name as user_lname')
		->join('restaurant','restaurant.id','=','review.restaurant_id')
		->join('user','user.id','=','review.customer_id')
		->where('restaurant.is_home_cooked','=','0')
		->orderBy($filter['field'], $filter['sort']);		
		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('user.first_name','like', '%'.session('filter.search').'%');		
		}
		if(Session::has('filter.restaurant_id') && session('filter.restaurant_id') !='All'){
			$q->where('review.restaurant_id','=', session('filter.restaurant_id'));
		}
		if(Session::has('filter.rating') && session('filter.rating') !='All'){
			$q->where('review.rating','=', session('filter.rating'));
		}
		if(Session::has('access.role') && Session::get('access.role') =='manager'){			
			$q->whereIn('review.restaurant_id', Session::get('access.restaurant_ids'));
		}		
						
		
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}

		return view('admin.review.index',['data'=>$data,'filter'=>$filter]);
		//echo "testig..";die;
	}
	public function add(Request $request){

		if($request->isMethod('post')) {
		    $this->validate($request,Review::$rules['create']);
		    $input = $request->all();
		    Review::dateFormat($input,'Y-m-d');		    		    
		    Review::create($input);
		    Session::flash('flash_message',  trans('admin.successfully.added'));
		    	
		    return redirect()->back();	        
	        
		}
		return view('admin.review.add');
	}
	public function edit($id){		
		if(Session::get('access.role') == 'manager'){
			$review = Review::whereIn('restaurant_id',Session::get('access.restaurant_ids'))->findOrFail($id);
		}else{
			$review = Review::findOrFail($id);
		}    	
    	Review::dateFormat($review,'d-m-Y');	    		  	
    	return view('admin.review.edit')->withReview($review);
		
	}
	public function update($id, Request $request)
	{
		if(Session::get('access.role') == 'manager'){
			$review = Review::whereIn('restaurant_id',Session::get('access.restaurant_ids'))->findOrFail($id);
		}else{
			$review = Review::findOrFail($id);
		}  

	    $this->validate($request, Review::$rules['edit']);

	    $input = $request->all();
	   	Review::dateFormat($input,'Y-m-d');
	   //	prd($input);
	    $review->fill($input)->save();

	    Session::flash('flash_message',  trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	public function delete($id,Request $request){
		if(Session::get('access.role') == 'manager'){
			$review = Review::whereIn('restaurant_id',Session::get('access.restaurant_ids'))->findOrFail($id);
		}else{
			$review = Review::findOrFail($id);
		}
		$review->delete();		
		Session::flash('flash_message',  trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/review');
	}	

}