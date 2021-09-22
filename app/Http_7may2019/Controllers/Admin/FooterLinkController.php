<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\FooterLinks;
use Illuminate\Contracts\Auth\Registrar;
use \Exception;
use Form;
use Illuminate\Pagination\Paginator;

class FooterLinkController extends \App\Http\Controllers\Controller {

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
		$q = FooterLinks::lang()->orderBy($filter['field'], $filter['sort']);			
		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('item_type','like', '%'.session('filter.search').'%');
		
		}
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}		
		return view('admin.footerlinks.index',['data'=>$data,'filter'=>$filter]);		
	}
	public function add(Request $request){

		if($request->isMethod('post')) {
		    $this->validate($request, [
		        'name' => 'required'
		    ]);
		    $input = $request->all();
		    FooterLinks::create($input);
		    Session::flash('flash_message',  trans('admin.successfully.added'));		   
		    return redirect()->back();	        
	        
		}
		return view('admin.footerlinks.add');
	}
	public function edit($id){
    	$footerLinks = FooterLinks::findOrFail($id);    	
    	return view('admin.footerlinks.edit',['footerLinks'=>$footerLinks]);
		
	}
	public function update($id, Request $request)
	{
		//echo "testing..";die;
	    $footerLinks = FooterLinks::findOrFail($id);

	    $this->validate($request, [
	        'name' => 'required'	       
	    ]);
	    $input = $request->all();

	    $footerLinks->fill($input)->save();

	    Session::flash('flash_message',  trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	public function delete($id){
		//echo "testing...";die;
		$footerLinks = FooterLinks::find($id);

		$footerLinks->delete();		
		Session::flash('flash_message',  trans('admin.successfully.deleted'));
		return redirect('admin/footer-links');
	}	

}