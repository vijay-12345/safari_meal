@extends('admin.layout')

@section('title', trans('admin.reports.customer'))

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
							{{trans('admin.reports.customer')}}
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
										<td>
											<input type="text" name="name" id="name" value="{{ Request::get('name') }}" placeholder="Name">
											<!-- <input type="email" name="email" id="email" value="{{ Request::get('email') }}" placeholder="Email"> -->
											<input type="text" name="email" id="email" value="{{ Request::get('email') }}" placeholder="Email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"  oninvalid="this.setCustomValidity('Please enter valid email')">
											<input type="text" maxlength = "10" name="phone" id="phone" value="{{ Request::get('phone') }}" placeholder="Phone">

											<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
											<button type="button" id="reset" class="btn btn-primary" value="{{trans('admin.reset')}}">{{trans('admin.reset')}}</button>

											<a href="{{ url('admin/reports/customer/export?name='.Request::get('name').'&email='.Request::get('email').'&phone='.Request::get('phone')) }}" class='btn btn-primary btn-md btn-default'>Export</a>
										</td>
									</tr>
								</table>
							</form>
							@if($data != null && $data->total() )
			                    <p>{{ 'Showing '.$data->firstItem().'-'.$data->lastItem().' of '.$data->total() }}</p>
			                @endif


			                <div class='table-responsive'>
								<table class="table table-striped">
									<thead>
									    <tr>					           
										    <th>Image</th>
										    <th>Name</th>
										    <th>Email</th>
										    <th>Phone</th>
										    <th>Orders</th>
										    <th>Order Amount</th>
									    </tr>
									</thead>
									<tbody>
										@if($data == null || $data->isEmpty() )
											<tr>
												<td colspan="6">No records found.</td>
											</tr>
										@else
											@foreach($data as $key => $val)
												<tr>
													<td><img src="{{ $val->profile_image ? $val->profile_image : url('images/cart-empty.png')  }}" width='50' class='img-circle' height="50" alt="{{ $val->first_name }}"></td>
													<td>{{ trim($val->first_name.' '.$val->last_name) }}</td>
													<td><a href="mailto:{{ $val->email }}">{{ $val->email }}</a></td>
													<td>{{ $val->contact_number }}</td>
													<td>{{ (int) $val->orders }}</td>
													<td>{{ (float) $val->amount }}</td>
												</tr>
											@endforeach
										@endif
									</tbody>
								</table>
							</div>
							@if($data != null)
							{!! $data->appends( Request::all() )->render() !!}
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection