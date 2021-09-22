<?php

namespace App\Http\Controllers\Admin\Reports\Admin;

use Auth, Session, DB, Request, Exception;
use App\Restaurent, App\User, App\Order, App\Payout;
use App\Traits\Export;

class OrderSalesController extends \App\Http\Controllers\Controller
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
		
		$date = Request::get('date') ? Request::get('date') : date('Y-m-d');

		$data = DB::table('restaurant as r')
			->where('r.status', 1)
			->leftJoin('order as o', function($join) use ($date) {
				$join->on('r.id', '=', 'o.restaurant_id');
				$join->where(DB::raw("DATE(o.created_at)"), '=', $date);
			})
			->select('r.id as restaurant_id', 'r.name', 'r.phone', 'r.restaurent_urlalias')
			->addSelect(
				DB::raw("sum(case when date(o.created_at)='".$date."' then 1 else 0 end) as total_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.pending')."' then 1 else 0 end) as pending_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.open_for_drivers')."' then 1 else 0 end) as open_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.accepted_by_driver')."' then 1 else 0 end) accepted_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.dispatched')."' then 1 else 0 end) as dispatched_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.delivered')."' then 1 else 0 end) as delivered_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.cancelled')."' then 1 else 0 end) as cancelled_orders")
			)->groupBy('r.id');

		$name = Request::get('name');
		if( $name ) {
			$data->where('r.name', 'like', '%'.$name.'%');
		}

		$data = $data->paginate( $per_page );

        return view('admin.reports.admin.order-sales', [
        	'data' 	=>	$data
        ]);
	}

	// Export report
	public function export()
	{
		$date = Request::get('date') ? Request::get('date') : date('Y-m-d');

		$data = DB::table('restaurant as r')
			->where('r.status', 1)
			->leftJoin('order as o', function($join) use ($date) {
				$join->on('r.id', '=', 'o.restaurant_id');
				$join->where(DB::raw("DATE(o.created_at)"), '=', $date);
			})
			->select('r.name', 'r.phone')
			->addSelect(
				DB::raw("sum(case when date(o.created_at)='".$date."' then 1 else 0 end) as total_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.pending')."' then 1 else 0 end) as pending_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.open_for_drivers')."' then 1 else 0 end) as open_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.accepted_by_driver')."' then 1 else 0 end) accepted_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.dispatched')."' then 1 else 0 end) as dispatched_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.delivered')."' then 1 else 0 end) as delivered_orders"),
				DB::raw("sum(case when date(o.created_at)='".$date."' and o.status='".config('constants.order.cancelled')."' then 1 else 0 end) as cancelled_orders")
			)->groupBy('r.id');

		$name = Request::get('name');
		if( $name ) {
			$data->where('r.name', 'like', '%'.$name.'%');
		}

		$data = $data->get();

		$this->setFileName('order-sales-report');
        $this->setHeaders( array('Restaurant Name', 'Phone', 'Total Orders', config('constants.order_status_label.admin.1'), config('constants.order_status_label.admin.2'), config('constants.order_status_label.admin.3'), config('constants.order_status_label.admin.4'), config('constants.order_status_label.admin.5'), config('constants.order_status_label.admin.6')) );

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
