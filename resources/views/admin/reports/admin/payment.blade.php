@extends('admin.layout')

@section('title', trans('admin.reports.payment'))

@section('content')

<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
			</div>

			<div class="col-md-9">
				<div class="page-data-right">
					<div class="panel panel-default">
						<div class="panel-heading">
							{{trans('admin.reports.payment')}}
						</div>

						<div class="panel-body">
							@if(Session::has('success'))
							    <div class="alert alert-success">
							        {{ session('success') }}	   
							    </div>
							@endif
							
							<form action="" method='get' id='filterForm'>
							    <input type="hidden" name="page" value="1" id='page'>
								<table class="table table-responsive filter-inner-form">
									<tr>
										<td colspan="3">
											<input type="text" name="start_date" id="start_date" value="{{ Request::get('start_date') }}" class='datepicker' placeholder="Start Date">
											<input type="text" name="end_date" id="end_date" value="{{ Request::get('end_date') }}" class='datepicker' placeholder="End Date">
											<!-- add filter of restaurant -->
											<input type="text" name="restaurant_name" id="restaurant_name" value="{{ Request::get('restaurant_name') }}" placeholder="Restaurant">
											<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
											<button type="button" id="reset" class="btn btn-primary" value="{{trans('admin.reset')}}">{{trans('admin.reset')}}</button>
											
											<a href="{{ url('admin/reports/payment/export?start_date='.Request::get('start_date').'&end_date='.Request::get('end_date')) }}" class='btn btn-primary btn-md btn-default'>Export</a>
										</td>
									</tr>
									<tr>
										<td>&#8377; {{ $amount }} <br>
											<strong>Total</strong>
										</td>
										<td align="center">&#8377; {{ $commission }} <br>
											<strong>Commission</strong>
										</td>
										<td align="right"> {{ $orders }} <br>
											<strong>Orders</strong>
										</td>
									</tr>
								</table>
							</form>
							
							@if( $data->total() )
			                    <p>{{ 'Showing '.$data->firstItem().'-'.$data->lastItem().' of '.$data->total() }}</p>
			                @endif
			                
			                <div class='table-responsive'>
								<table class="table table-striped">
									<thead>
									    <tr>					           
										    <th>Restaurant</th>
										    <!-- <th>Commission %</th> -->
										    <th>Total Orders</th>
										    <th>Sub Total</th>
										    <th>Total Amount</th>
										    <th>Total Commission</th>
										    <th>Net amount to be paid</th>
										    <th>Amount already paid</th>
										    <th>Amount Pending</th>
									    </tr>
									</thead>
						
									<?php $varbool='true'; ?>
									@foreach($data as $key => $val)
										@if($val->orders == 0)
											<?php continue; ?>
										@else
											<?php $varbool='false'; ?>	
										@endif		
									@endforeach
									<tbody>
										
										@if($varbool == 'true')
										<tr>
											<td colspan="8">No records found.</td>
										</tr>
										@else
											@foreach($data as $key => $val)
												<tr>

													<td><a href="{{ url('admin/reports/payout/'.$val->restaurent_urlalias) }}" target='_blank'>{{ $val->restaurant }}</a></td>
													<!-- <td>{{ $val->admin_commission.' %' }}</td> -->
													<td>{{ (int) $val->orders }}</td>
													<td>{{ (int) $val->subtotal }}</td>
													<td>&#8377; {{ (float) $val->amount }}</td>
													<td>&#8377; {{ (float) $val->commission }}</td>
													<td>&#8377; {{ (float) ($val->amount - $val->commission) }}</td>
													<td>&#8377; {{ (float) $val->paid_amount }}</td>
													<td>
														&#8377; {{ (float) $val->unpaid_amount }} 
														@if( $val->unpaid_amount > 0 )
															<button class='btn btn-sm btn-primary pay' data-restaurant="{{ $val->restaurant_id }}">Mark as paid</button>
														@endif
													</td>
												</tr>
											@endforeach
										@endif
									</tbody>
								</table>
							</div>
							{!! $data->appends( Request::all() )->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ** Date range script ** -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
var json = {
	'start_date': "{{ Request::get('start_date') }}",
	'end_date': "{{ Request::get('end_date') }}",
	'restaurant_name': "{{ Request::get('restaurant_name') }}"
}

jQuery(function($){
	$('.datepicker').datepicker({
		dateFormat:"yy-mm",
		defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        // onClose: function(dateText, inst) { 
        //     $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        // }
        onClose: function(dateText, inst) { 
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        }
    });

    // Mark as paid
    $(document).on('click', '.pay', function(){
    	if( confirm('Are you sure you want to proceed? This action can not be undone.') )
    	{
    		var obj = $(this);
	    	json.restaurant = $(this).data('restaurant');
	    	obj.html('Please wait...').attr('disabled', true);
			$.post(baseUrl + 'admin/reports/payment/pay', json, function(response){
				response = $.parseJSON( response );
				if( response.status == 'success' ) {
					window.location.reload();
				} else {
					alert(response.message);
					obj.html('Mark as paid').attr('disabled', false);
				}
			});
		}
    });
});

</script>

<style type="text/css">
.ui-datepicker-calendar {
	/*display: none;*/
}	
</style>

@endsection