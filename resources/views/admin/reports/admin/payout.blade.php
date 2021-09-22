@extends('admin.layout')

@section('title', trans('admin.reports.payout').' - '.$restaurant->name)

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
							{{trans('admin.reports.payout')}}
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
											
											<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
											<button type="button" id="reset" class="btn btn-primary" value="{{trans('admin.reset')}}">{{trans('admin.reset')}}</button>
											
										</td>
									</tr>
								</table>
							</form>

							<!-- {{print_r($data)}} -->

							@if( $data->total() )
			                    <p>{{ 'Showing '.$data->firstItem().'-'.$data->lastItem().' of '.$data->total() }}</p>
			                @endif

			                <div class='table-responsive'>
								<table class="table table-striped">
									<thead>
									    <tr>
										    <th>Restaurant</th>
										    <th>Amount</th>
										    <th>Date</th>
									    </tr>
									</thead>
									<tbody>
										@if( $data->isEmpty() )
											<tr>
												<td colspan="3">No records found.</td>
											</tr>
										@else
											@foreach($data as $key => $val)
												<tr>
													<td>{{ $val->name }}</td>
													<td>&#8377; {{ (float) $val->amount }}</td>
													<td>{{ date('M d, Y', strtotime($val->created_at)).' at '.date('H:i a', strtotime($val->created_at)) }}</td>
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
jQuery(function($){
	$('.datepicker').datepicker({
		dateFormat:"yy-mm",
		defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        onClose: function(dateText, inst) { 
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        }
    });

	// Reset Filter Form
    $('#reset').click(function(){
    	$('#filterForm .form-control').val('');
    	$('#filterForm').submit();
    });
});

</script>

<style type="text/css">
.ui-datepicker-calendar {
	/*display: none;*/
}	
</style>

@endsection