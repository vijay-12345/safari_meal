<?php

namespace App\Http\Controllers\Admin\Reports\Admin;

use Auth, Session, DB, Request, Exception;
use App\Restaurent, App\User, App\Payout;
use App\Order;
use App\Traits\Export;
use Carbon\Carbon;


class PaymentController extends \App\Http\Controllers\Controller
{
	use Export;

	private $per_page = 10;

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
		// echo "good morning";
		$data = $this->getData();
		// $data = array();
		// foreach($data as $key => $val)
		// {
		// 	if($var->orders>0)
		// 	{
		// 		array_push($data,$val);
		// 	}
		// }		
		$items = $data->get();
		$data = $data->paginate( $this->per_page );
		//ddd($data);
        return view('admin.reports.admin.payment', [
        	'data' 			=>	$data,
        	'orders' 		=>	collect($items)->sum('orders'),
        	'amount' 		=>	collect($items)->sum('amount'),
        	'commission' 	=>	collect($items)->sum('commission')
        ]);
	}

	public function getData()
	{
		// $mytime = Carbon::now();
		// $mytime_format = Carbon::parse($mytime)->format('Y-m');
		$start_date = Request::get('start_date');
		// if(!$start_date)
		// 	$start_date = date('1500-01');
		$end_date = Request::get('end_date');
		// if(!$end_date)
		// {
		// 	$end_date = $mytime_format;
		// }
		$restaurant_data = Request::get('restaurant_name');
		// print_r($restaurant_data);
		// die;
		// if(!$restaurant_data)
		// {
		// 	$restaurant_data = "";
		// }
		
		// $data = DB::table('restaurant as r')
		// 	->select( DB::raw("o.restaurant_id,
		// 		r.restaurent_urlalias, 
		// 		r.admin_commission,
		// 		r.name as restaurant,
		// 		count(o.status) as orders,
		// 		round(amount-o.shipping_charge-o.packaging_fees-o.cgst-o.sgst) as subtotal,
		// 		sum(amount) as amount,
		// 		round(sum(o.commission), 2) as commission,
		// 		(sum(amount) - round(sum(o.commission), 2)) as net_amount,
		// 		(sum(case when paid_by_admin=1 then (amount-o.commission) end)) as paid_amount,
		// 		(sum(case when paid_by_admin=0 then (amount-o.commission) end)) as unpaid_amount") )
		// 	->orWhere( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '>=', $start_date)
		// 	->orWhere( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '<=', $end_date)
		// 	->orwhere('r.name', 'like','%' .$restaurant_data. '%')
		// 	->leftJoin('order as o', function( $join ){
		// 		$join->on('r.id', '=', 'o.restaurant_id');
		// 		$join->where('o.status', '=', config('constants.order.delivered'));
				
		// 	})->groupBy('r.id');

		if($start_date && $end_date && $restaurant_data){
		$data = DB::table('restaurant as r')
			->select( DB::raw("o.restaurant_id,
				r.restaurent_urlalias, 
				r.admin_commission,
				r.name as restaurant,
				count(o.status) as orders,
				round(amount-o.shipping_charge-o.packaging_fees-o.cgst-o.sgst) as subtotal,
				sum(amount) as amount,
				round(sum(o.commission), 2) as commission,
				(sum(amount) - round(sum(o.commission), 2)) as net_amount,
				(sum(case when paid_by_admin=1 then (amount-o.commission) end)) as paid_amount,
				(sum(case when paid_by_admin=0 then (amount-o.commission) end)) as unpaid_amount") )
			->Where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '>=', $start_date)
			->Where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '<=', $end_date)
			->where('r.name', 'like',$restaurant_data. '%')
			->leftJoin('order as o', function( $join ){
				$join->on('r.id', '=', 'o.restaurant_id');
				$join->where('o.status', '=', config('constants.order.delivered'));
				
			})->groupBy('r.id');
		}

		else if($start_date && $end_date){
		$data = DB::table('restaurant as r')
			->select( DB::raw("o.restaurant_id,
				r.restaurent_urlalias, 
				r.admin_commission,
				r.name as restaurant,
				count(o.status) as orders,
				round(amount-o.shipping_charge-o.packaging_fees-o.cgst-o.sgst) as subtotal,
				sum(amount) as amount,
				round(sum(o.commission), 2) as commission,
				(sum(amount) - round(sum(o.commission), 2)) as net_amount,
				(sum(case when paid_by_admin=1 then (amount-o.commission) end)) as paid_amount,
				(sum(case when paid_by_admin=0 then (amount-o.commission) end)) as unpaid_amount") )
			->Where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '>=', $start_date)
			->Where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '<=', $end_date)
			->leftJoin('order as o', function( $join ){
				$join->on('r.id', '=', 'o.restaurant_id');
				$join->where('o.status', '=', config('constants.order.delivered'));
				
			})->groupBy('r.id');
		}

		else if($restaurant_data){
		$data = DB::table('restaurant as r')
			->select( DB::raw("o.restaurant_id,
				r.restaurent_urlalias, 
				r.admin_commission,
				r.name as restaurant,
				count(o.status) as orders,
				round(amount-o.shipping_charge-o.packaging_fees-o.cgst-o.sgst) as subtotal,
				sum(amount) as amount,
				round(sum(o.commission), 2) as commission,
				(sum(amount) - round(sum(o.commission), 2)) as net_amount,
				(sum(case when paid_by_admin=1 then (amount-o.commission) end)) as paid_amount,
				(sum(case when paid_by_admin=0 then (amount-o.commission) end)) as unpaid_amount") )
			->where('r.name', 'like',$restaurant_data. '%')
			->leftJoin('order as o', function( $join ){
				$join->on('r.id', '=', 'o.restaurant_id');
				$join->where('o.status', '=', config('constants.order.delivered'));
				//restaurant filter
			})->groupBy('r.id');

			// print_r($restaurant_data);
			// die;
		}
		else{	
		$data = DB::table('restaurant as r')
			->select( DB::raw("o.restaurant_id,
				r.restaurent_urlalias, 
				r.admin_commission,
				r.name as restaurant,
				count(o.status) as orders,
				round(amount-o.shipping_charge-o.packaging_fees-o.cgst-o.sgst) as subtotal,
				sum(amount) as amount,
				round(sum(o.commission), 2) as commission,
				(sum(amount) - round(sum(o.commission), 2)) as net_amount,
				(sum(case when paid_by_admin=1 then (amount-o.commission) end)) as paid_amount,
				(sum(case when paid_by_admin=0 then (amount-o.commission) end)) as unpaid_amount") )
			->leftJoin('order as o', function( $join ){
				$join->on('r.id', '=', 'o.restaurant_id');
				$join->where('o.status', '=', config('constants.order.delivered'));
				
				// $start_date = Request::get('start_date');
				// if( $start_date ) {
				// 	$join->where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '>=', $start_date);
				// }

				// $end_date = Request::get('end_date');
				// if( $end_date ) {
				// 	$join->where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '<=', $end_date);
				// }

				//restaurant filter
				//$restaurant_data = Request::get('restaurant_name');
				// if( $restaurant_data ) {
				// 	$join->where('r.name', 'like','%' .$restaurant_data. '%');
				// }
			})->groupBy('r.id');
		}


		return $data;
	}

	public function pay()
	{
		$status = 'error';
		$message = null;
		$params = Request::all();
		try
		{
			// Check if there is any amount left to be paid? 
			$query = DB::table('restaurant as r')
				->select( DB::raw("o.restaurant_id,
					r.admin_commission,
					r.name as restaurant,
					count(1) as orders,
					sum(amount) as amount,
					round(sum(o.commission), 2) as commission,
					(sum(case when paid_by_admin=1 then (amount-o.commission) end)) as paid_amount,
					(sum(case when paid_by_admin=0 then (amount-o.commission) end)) as unpaid_amount") )
				->where('r.id', $params['restaurant'])
				->leftJoin('order as o', function( $join ){ 
					$join->on('r.id', '=', 'o.restaurant_id');
					$join->where('o.status', '=', config('constants.order.delivered'));

					$start_date = Request::get('start_date');
					if( $start_date ) {
						$join->where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '>=', $start_date);
					}
					
					$end_date = Request::get('end_date');
					if( $end_date ) {
						$join->where( DB::raw("DATE_FORMAT(o.created_at, '%Y-%m')"), '<=', $end_date);
					}

				})->groupBy('r.id');
				
			$data = $query->first();
			if( ! $data ) {
				throw new Exception('No amount to be paid', 1);
			}
			
			// Mark all orders in this interval as paid
			$orders = Order::where('restaurant_id', $params['restaurant'])
				->where('status', config('constants.order.delivered'));

			if( $params['start_date'] ) {
				$orders->whereRaw("DATE_FORMAT(created_at, '%Y-%m') >= '".$params['start_date']."'");
			}

			if( $params['end_date'] ) {
				$orders->whereRaw("DATE_FORMAT(created_at, '%Y-%m') <= '".$params['start_date']."'");
			}

			$updated = $orders->update(['paid_by_admin' => 1]);
			
			// Log record
			$json = array(
				'total_orders' 			=> (int) $data->orders,
				'total_amount'			=> (float) $data->amount,
				// 'commission_percentage' => (float) $data->admin_commission,
				'commission_amount'		=> (float) $data->commission,
				'net_amount'			=> (float) ($data->amount - $data->commission),
				'amount_already_paid'	=> (float) $data->paid_amount,
				'amount_paid'			=> (float) $data->unpaid_amount,
				'start_date'			=> $params['start_date'] ? $params['start_date'] : null,
				'end_date'				=> $params['end_date'] ? $params['end_date'] : date('Y-m-d')
			);

			$model = new Payout;
			$model->restaurant_id = $params['restaurant'];
			$model->json = json_encode( $json );
			$model->amount = (float) $data->unpaid_amount;
			$model->save();

			$status = 'success';
			
		} catch( Exception $e ) {
			$message = $e->getMessage();
		}

		return json_encode( array( 
			'status' 	=> $status,
			'message' 	=> $message
		));
	}

	// Export report
	public function export()
	{
		$data = $this->getData()->get();

		$this->setFileName('payment-report');
        $this->setHeaders(array('Restaurant', 'Total orders', 'Sub Total', 'Total amount', 'Total commission', 'Net amount to be paid', 'Amount already paid', 'Amount pending'));

        $this->setOutput( function() use ($data) {
            $output = '';
            foreach ($data as $key => $value) {
            	$temp = (array) $value;
            	array_shift($temp);
            	array_shift($temp);
            	array_shift($temp);
                $output .= implode("\t", $temp)."\n";
            }
            // echo $output;die;
            return $output;
        });
        
        $this->exportFinally();
	}
	
}