<?php

namespace App\Http\Controllers\Admin\Reports\Admin;

use Auth, Session, DB, Request, Exception;
use App\Restaurent, App\User, App\Order;
use App\Traits\Export;

class CustomerController extends \App\Http\Controllers\Controller
{
	use Export;

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
	 * Show the reports to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$per_page = 10;
		$data = DB::table('user as u')
			->leftJoin('order as o', 'u.id', '=', 'o.user_id')
			->where('u.role_id', config('constants.user.customer'))
			->select('u.first_name', 'u.last_name', 'u.profile_image', 'u.email', 'u.contact_number', DB::raw('SUM(o.amount) as amount'), DB::raw('count(o.status) as orders'))
			->groupBy('u.id');
		
		$name = Request::get('name');
		if( $name ) {
			//handle blank input
			$name = trim($name);
			if($name == '')
			{
				$data =null;
				return view('admin.reports.admin.customer', [
        	                'data' 	=>	$data
        					]);
			}
			$data->where('u.first_name', 'like', '%'.$name.'%');
		}

		$phone = Request::get('phone');
		if( $phone ) {
			//handle blank input
			$name = trim($phone);
			if($phone == '')
			{
				$data =null;
				return view('admin.reports.admin.customer', [
        	                'data' 	=>	$data
        					]);
			}
			$data->where('u.contact_number', $phone);
		}

		$email = Request::get('email');
		if( $email ) {
			//handle blank input
			$email = trim($email);
			if($email == '')
			{
				$data =null;
				return view('admin.reports.admin.customer', [
        	                'data' 	=>	$data
        					]);
			}
			$data->where('u.email', $email);
		}

		$data = $data->paginate( $per_page );

        return view('admin.reports.admin.customer', [
        	'data' 	=>	$data
        ]);
	}

	// Export report
	public function export()
	{
		$data = DB::table('user as u')
			->leftJoin('order as o', 'u.id', '=', 'o.user_id')
			->where('u.role_id', config('constants.user.customer'))
			->select(DB::raw(" concat(u.first_name, ' ', u.last_name) as name "), 'u.email', 'u.contact_number', DB::raw('count(o.status) as orders'), DB::raw('SUM(o.amount) as amount'))
			->groupBy('u.id');
		
		$name = Request::get('name');
		if( $name ) {
			$data->where('u.first_name', 'like', '%'.$name.'%');
		}

		$phone = Request::get('phone');
		if( $phone ) {
			$data->where('u.contact_number', $phone);
		}

		$email = Request::get('email');
		if( $email ) {
			$data->where('u.email', $email);
		}

		$data = $data->get();

		$this->setFileName('customer-report');
        $this->setHeaders( array('Name', 'Email', 'Phone', 'Total Orders', 'Total amount') );

        $this->setOutput( function() use ($data) {
            $output = '';
            foreach ($data as $key => $value) {
                $output .= implode("\t", (array) $value)."\n";
            }
            
            return $output;
        });
        
        $this->exportFinally();
	}
}
