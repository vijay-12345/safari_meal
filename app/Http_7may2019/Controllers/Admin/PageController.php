<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\Page;
use Illuminate\Contracts\Auth\Registrar;
use \Exception;
use Form;
use Illuminate\Pagination\Paginator;

class PageController extends \App\Http\Controllers\Controller {

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
			$filter = ['sort'=>'desc','field'=>'title'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}		
		
		$data = [];		
		$q = Page::lang()
			->orderBy($filter['field'], $filter['sort']);		
		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('title','like', '%'.session('filter.search').'%');
		
		}					
		
		$data['data'] = $q->paginate(session('filter.paginate_limit'));
		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}
		return view('admin.page.index',['data'=>$data,'filter'=>$filter]);
		
	}
	public function add(Request $request){

		if($request->isMethod('post')) {
		    $this->validate($request,Page::$rules['create']);
		    $input = $request->all();		    
		    Page::create($input);
		    $page_id = DB::getPdo()->lastInsertId();
		    if(empty($input['page_urlalias'])){
			    $page_urlalias = str_slug($input['title']).'-ps'.$page_id;
			    Page::where('id',$page_id)->update(['page_urlalias'=>$page_urlalias]);		    	
		    }

		    Session::flash('flash_message',  trans('admin.successfully.added'));
		    	
		    return redirect()->back();	        
	        
		}
		return view('admin.page.add');
	}
	public function edit($id){
    	$page = Page::findOrFail($id);    		    		  	
    	return view('admin.page.edit')->withPage($page);
		
	}
	public function update($id, Request $request)
	{
		
	    $page = Page::findOrFail($id);	    
	    $this->validate($request,Page::$rules['edit']);

	    $input = $request->all();
	    $page->fill($input)->save();

	    Session::flash('flash_message',  trans('admin.successfully.updated'));

	    return redirect()->back();
	}
	public function delete($id){
		
		$page = Page::find($id);

		$page->delete();		
		Session::flash('flash_message',  trans('admin.successfully.deleted'));
		return redirect('admin/page');
	}	

}