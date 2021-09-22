<?php  
use App\City;
use App\User;
?>
@extends('publicLayout')
@section('title', 'Edit Profile')
@section('content')
<?php 
	//$currentUser = Auth::user();		
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
							<li class="dropdown keep-open open">
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
							<li><a href="{{url('/addressbook')}}" title=""><i class="flaticon-gps-1"></i>Address Book
								<!-- <span class="flaticon-arrows-1"></span> -->
							</a></li>
							<li><a href="{{url('order-history')}}" title=""><i class="flaticon-coins"></i>Order History
								<!-- <span class="flaticon-arrows-1"></span> -->
							</a></li>
						</ul>
					</div>
				</div>

				<div class="col-md-8">
					<div class="page-data-right">
						<div class="page-data-header">
							<i class="flaticon-cogwheel"></i>
							Account Settings							
						</div>						
						<div class="page-data-outer">
							{!! Form::model($currentUser, array('url' => 'updateprofile')) !!}								
							<div class="row">
								@if (session('status'))
								    <div class="alert alert-success">
								        {{ session('status') }}
								    </div>
								@endif
								<div class="col-md-8 col-md-offset-2">
									<div class="form-cont">
										<h5>Personal Information</h5>
										<div class="form-group">											
											<input type="hidden" name="id" value="{{$currentUser->id}}" placeholder="First Name" class="form-control icon-field">
											<!-- input type="text" name="first_name" value="{{$currentUser->first_name}}" placeholder="First Name" class="form-control icon-field" -->
											{!! Form::text('first_name', null, ['placeholder' => 'First Name', 'class' => 'form-control icon-field']) !!}
											{!! Form::errorMsg('first_name', $errors) !!}
											<i class="flaticon-shape"></i>
										</div>
										<div class="form-group">
											<!--input type="text" name="last_name" value="{{$currentUser->last_name}}" placeholder="Last Name" class="form-control icon-field" -->
											{!! Form::text('last_name', null, ['placeholder' => 'Last Name', 'class' => 'form-control icon-field']) !!}
											{!! Form::errorMsg('last_name', $errors) !!}
											<i class="flaticon-shape"></i>
										</div>
										<h5>Contact Details</h5>
										<div class="form-group phone-field">
					        		<!--select class="country-code" name="coun">
					        			<option>+91</option>
					        		</select-->
									{!! Form::hidden('countrycode', null, ['placeholder' => 'contorycode Number','class' => 'form-control icon-field']) !!}
					        		
									<select class="country-code" name="countrycodeoption">
										<option value="241">+241</option>
					        		@foreach($country_code as $country_cod)
									<option value="{{$country_cod->phonecode}}">+{{$country_cod->phonecode}}</option>
									@endforeach	 
					        		<!--input type="text" name="contact_number" value="{{$currentUser->contact_number}}" placeholder="Mobile Number" class="form-control icon-field" -->
									
					        		{!! Form::text('contact_number', null, ['placeholder' => 'Mobile Number', 'class' => 'form-control icon-field']) !!}
									{!! Form::errorMsg('contact_number', $errors) !!}
					        		<i class="flaticon-technology"></i>
					        	</div>
										<div class="form-group">
					        		<!--input type="text" name="email" value="{{$currentUser->email}}" placeholder="Email ID" class="form-control icon-field" -->
					        		{!! Form::text('email', null, ['placeholder' => 'Email ID', 'class' => 'form-control icon-field']) !!}
									{!! Form::errorMsg('email', $errors) !!}									
									
					        		<i class="flaticon-letter"></i>
					        	</div>
					        	<h5>Newsletter</h5>
					        	<p>Recieve order and deal from hellofood.</p>
					        	<div class="term-cont">	
					        	
										{!! Form::checkbox('newsletter',null,null, ['class' => 'css-checkbox',"id"=>"checkboxrG5"]) !!}
										<label for="checkboxrG5" class="css-label">Subscribe to NewsLetter</label>
										</div>
										<div class="btn-cont margin-top">
				        			<button type="submit" class="btn btn-primary btn-lg btn-full">Save</button>
				        		</div>
									</div>
								</div>
							</div>
							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--/inner page data-->
@endsection
