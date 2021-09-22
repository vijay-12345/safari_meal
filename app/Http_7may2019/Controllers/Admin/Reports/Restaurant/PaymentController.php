<?php

namespace App\Http\Controllers\Admin\Reports\Restaurant;

use Auth, Session, DB, Request;
use App\Restaurent, App\User, App\Order;

class PaymentController extends \App\Http\Controllers\Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	// Report
	public function index()
	{
		$restaurant = Restaurent::where('owner_id', Auth::user()->id)->first();
		$restaurant_id = $restaurant ? $restaurant->id : 0;
		
		$per_page = 10;
		$data = DB::table('order as o')
			->select( DB::raw("DATE_FORMAT(o.created_at,'%Y-%m') as period, count(1) as orders, sum(amount) as amount, round((r.admin_commission/100 * sum(amount)), 2) as commission, (sum(amount)-round((r.admin_commission/100 * sum(amount)), 2)) as sales") )
			->join('restaurant as r', 'o.restaurant_id', '=', 'r.id')
			->where('o.status', config('constants.order.delivered'))
			->where('o.restaurant_id', $restaurant_id)
			->groupBy( DB::raw("DATE_FORMAT(o.created_at,'%Y-%m')") );
			
		$start_date = Request::get('start_date');
		if( $start_date ) {
			$data->whereRaw("DATE_FORMAT(o.created_at, '%Y-%m') >= '".$start_date."'");
		}

		$end_date = Request::get('end_date');
		if( $end_date ) {
			$data->whereRaw("DATE_FORMAT(o.created_at, '%Y-%m') <='".$end_date."'");
		}

		$items = $data->get();
		$data = $data->paginate( $per_page );
					
        return view('admin.reports.restaurant.payment', [
        	'data' 			=>	$data,
        	'orders' 		=>	collect($items)->sum('orders'),
        	'amount' 		=>	collect($items)->sum('amount'),
        	'commission' 	=>	collect($items)->sum('commission')
        ]);
	}

	// Export report
	public function export()
	{
		$data = DB::table('order as o')
			->select( DB::raw("DATE_FORMAT(o.created_at,'%Y-%m') as period, count(1) as orders, sum(amount) as amount, round((r.admin_commission/100 * sum(amount)), 2) as commission, (sum(amount)-round((r.admin_commission/100 * sum(amount)), 2)) as sales") )
			->join('restaurant as r', 'o.restaurant_id', '=', 'r.id')
			->where('o.status', config('constants.order.delivered'))
			->where('o.restaurant_id', 6)
			->groupBy( DB::raw("DATE_FORMAT(o.created_at,'%Y-%m')") );

		$start_date = Request::get('start_date');
		if( $start_date ) {
			$data->whereRaw("DATE_FORMAT(o.created_at, '%Y-%m') >= '".$start_date."'");
		}

		$end_date = Request::get('end_date');
		if( $end_date ) {
			$data->whereRaw("DATE_FORMAT(o.created_at, '%Y-%m') <='".$end_date."'");
		}
		
		$data = $data->get();
        
        // Export
        $headers = array('Period', 'Orders', 'Total Amount', 'Admin Commission', 'Sales');
        $output = implode("\t", $headers)."\n";
        if( $data )
        {
        	foreach ($data as $key => $value) {
        		$output .= implode("\t", (array)$value)."\n";
        	}
        }

        $filename = strtolower('report-'.date('d').'-'.date('M').'-'.date('Y').'-'.time().".xls");		 
        header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		echo $output;
        exit;
	}
}
