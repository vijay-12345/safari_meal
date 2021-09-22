<?php

namespace App\Http\Controllers\Admin\Reports\Admin;

use Auth, Session, DB, Request, Exception;
use App\Restaurent, App\User, App\Order;
use App\Traits\Export;

class DriverController extends \App\Http\Controllers\Controller
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
			->leftJoin('order as o', function($join){
				$join->on('u.id', '=', 'o.driver_id');
				$join->where('o.order_type', '=', 'delivery');
			})
			->where('u.role_id', config('constants.user.driver'))
			->select('u.id', 'u.first_name', 'u.last_name', 'u.profile_image', 'u.email', 'u.contact_number')
			->addSelect(
				DB::raw('count(*) as total_orders'),
				DB::raw("SUM(case when o.status='".config('constants.order.delivered')."' then 1 else 0 end) as closed_orders"),
				DB::raw("SUM(case when o.status='".config('constants.order.cancelled')."' then 1 else 0 end) as cancelled_orders"),
				DB::raw("SUM(case when o.status='".config('constants.order.delivered')."' then amount end) as amount_received")
			)->groupBy('u.id');
		
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

		$data = $data->paginate( $per_page );

        return view('admin.reports.admin.driver', [
        	'data' 	=>	$data
        ]);
	}

	// Export report
	public function export()
	{
		$data = DB::table('user as u')
			->leftJoin('order as o', function($join){
				$join->on('u.id', '=', 'o.driver_id');
				$join->where('o.order_type', '=', 'delivery');
			})
			->where('u.role_id', config('constants.user.driver'))
			->select(DB::raw("concat(u.first_name, ' ', u.last_name)"), 'u.email', 'u.contact_number')
			->addSelect(
				DB::raw('count(*) as total_orders'),
				DB::raw("SUM(case when o.status='".config('constants.order.delivered')."' then 1 else 0 end) as closed_orders"),
				DB::raw("SUM(case when o.status='".config('constants.order.cancelled')."' then 1 else 0 end) as cancelled_orders"),
				DB::raw("SUM(case when o.status='".config('constants.order.delivered')."' then amount end) as amount_received")
			)->groupBy('u.id');
		
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

		$this->setFileName('delivery-executive-report');
        $this->setHeaders( array('Name', 'Email', 'Phone', 'Total Orders', 'Closed Orders', 'Cancelled Orders', 'Total amount') );

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
