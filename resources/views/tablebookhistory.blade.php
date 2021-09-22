<?php  
use App\City;
use App\User;
?>
@extends('publicLayout')

@section('title', 'Table Booking History')

@section('content')

<?php		
$cities = City::get();	
$country_code = DB::table('country_code')->select(DB::raw('DISTINCT(phonecode)'))->orderBy('phonecode', 'ASC')->where('phonecode','!=','241')->get();
?>

<div class="inner-page-header">
	<div class="container">
	<div class="row">
	<div class="col-md-9">
		<div class="breadcrumbs-cont">
			<p><a href="{{url('/')}}" title="">Home</a> / <a href="{{url('/editprofile')}}" title="">My Profile</a> /  <a href="{{url('/editprofile')}}" title="">Account Settings </a> </p>
		</div>
		</div>
		<div class="col-md-3">

		</div>
		</div>
	</div>
</div>	
	
	
<div class="inner-page-data">
	<div class="container">
		<div class="sidebar-data">
			<div class="row">
				<div class="col-md-4">
					<div class="sidebar-menu">
						<ul>
							<!-- <li><a href="javascript:void(0)" title=""><i class="flaticon-people"></i>Activity
								<span class="flaticon-arrows-1"></span>
							</a></li> -->
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><i class="flaticon-shape"></i>My Account <span class="flaticon-arrows-1"></span></a>
								<ul class="dropdown-menu">
									<li class="active"><a href="{{url('/editprofile')}}"><i class="flaticon-cogwheel"></i>Account Settings
										<!-- <span class="flaticon-arrows-1"></span> -->
									</a></li>
									<li><a href="{{url('/changepassword')}}"><i class="flaticon-lock"></i>Change Password
										<!-- <span class="flaticon-arrows-1"></span> -->
									</a></li>
								</ul>
							</li>
							<li><a href="{{url('/addressbook')}}" title=""><i class="flaticon-gps-1"></i>Address Book</a></li>
							<li class='keep-open'><a href="{{url('order-history')}}" title=""><i class="flaticon-coins"></i>Order History</a></li>
							<li class='keep-open open'><a href="{{url('tablebook-history')}}" title=""><i class="flaticon-coins"></i>Table Booking History</a></li>
						</ul>
					</div>
				</div>
				<div class="col-md-8">
					<div class="page-data-right">
						<div class="page-data-header">
							<i class="flaticon-cogwheel"></i>
							Order History
						</div>						
						<div class="page-data-outer">
							<div class="table-responsive">
								<table class="table table-stripped">
									<thead>
										<tr>
											<th>Order #</th>
											<th>Restaurant</th>
											<th>Booking Date</th>
											<th>Total Guest</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
										<tbody>
											@if($orders->isEmpty())
												<tr><td colspan="6">{{ trans('admin.record.not.found') }}</td></tr>
											@else
												@foreach($orders as $key => $val)
													<tr>
														<td>{{ $val->order_number }}</td>
														<td><a href="{{ url('restaurentdetail/'.$val->restaurent_urlalias) }}" target="_blank">{{ $val->restaurant_name }}</a></td>
														<td>{{ date('M d, Y', strtotime($val->book_date)) }}</td>
														<td>{{ $val->total_person }}</td>
														<td>{{ config('constants.order_status_label.admin.'.$val->status) }}</td>
														<td data-id="{{$val->id}}"><?php if($val->status == 1 && $val->book_date >= date('Y-m-d')){ ?><button type="button" class="btn btn-warning" class="cancel_booking" value="{{ $val->id }}">Cancel</button><?php } ?></td>
													</tr>
												@endforeach
											@endif
										</tbody>
									</thead>
								</table>
							</div>

							{!! $orders->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

$('body').on('click', 'button', function() {
    
    if(confirm('Are you sure to update this?')) {
        var id = $(this).val();
        var status = 6;
        $.ajax({
            url: '{{url('table/update-table-status')}}',
            type:'post',
            dataType:'json',
            data: "status="+status+"&id="+id,
            success:function(data) { 
                // $("#list_data"+id).html(data.list_data);  
                if(data.list_data == 'success'){  
                    alert('Updated Successfully.');
                    location.reload(true); 
                }                
            },
            error:function(error) {
                alert('Error.');
            }
        });
    }
});
	
</script>
@endsection