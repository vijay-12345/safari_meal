<?php  
use App\City;
use App\User;
?>
@extends('publicLayout')
@section('title', 'Change Password')
@section('content')
<?php 	
	$cities = City::get();	
	//pr($currentUser);
?>
<div class="inner-page-header">
	<div class="container">
	<div class="row">
	<div class="col-md-9">
		<div class="breadcrumbs-cont">
			<p><a href="{{url('/')}}" title="">Home</a> / <a href="{{url('/editprofile')}}" title="">My Profile</a> /  <a href="{{url('/changepassword')}}" title="">Change Password </a> </p>
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
			          			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><i class="flaticon-shape"></i>My Account 
			          				<span class="flaticon-arrows-1"></span>
			          			</a>
					          	<ul class="dropdown-menu">
						            <li><a href="{{url('/editprofile')}}"><i class="flaticon-cogwheel"></i>Account Settings
						            	<!-- <span class="flaticon-arrows-1"></span> -->
						            </a></li>
						            <li class="active"><a href="{{url('/changepassword')}}"><i class="flaticon-lock"></i>Change Password
						            	<!-- <span class="flaticon-arrows-1"></span> -->
						            </a></li>
					          	</ul>
					        </li>
			        		<li><a href="{{url('/addressbook')}}" title=""><i class="flaticon-gps-1"></i>Address Book
			        			<!-- <span class="flaticon-arrows-1"></span> -->
			        		</a></li>
							<li><a href="{{url('order-history')}}" title=""><i class="flaticon-coins"></i>Order History
								<!-- <span class="flaticon-arrows-1"></span></a> -->
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-8">
					<div class="page-data-right">
						<div class="page-data-header">
							<i class="flaticon-lock"></i>
							Change Password
						</div>
						<div class="page-data-outer">
								{!! Form::model($currentUser, array('url' => 'updatepassword')) !!}											
							<div class="row">
								@if (session('status'))
								    <div class="alert alert-success">
								        {{ session('status') }}
								    </div>
								@endif
								<div class="col-md-8 col-md-offset-2">
									<div class="form-cont">
										<div class="form-group">					        		
					        		{!! Form::password('old_password',  ['placeholder' => 'Current Password', 'class' => 'form-control icon-field']) !!}
									{!! Form::errorMsg('old_password', $errors) !!}
					        		<i class="flaticon-lock"></i>
					        	</div>
					        	<div class="form-group">					        		
					        		{!! Form::password('new_password',['placeholder' => 'New Password', 'class' => 'form-control icon-field']) !!}
									{!! Form::errorMsg('new_password', $errors) !!}
					        		<i class="flaticon-lock"></i>
					        	</div>
					        	<div class="form-group">					        		
					        		{!! Form::password('new_password_confirmation', ['placeholder' => 'Confirm Password', 'class' => 'form-control icon-field']) !!}
									{!! Form::errorMsg('new_password_confirmation', $errors) !!}
					        		<i class="flaticon-lock"></i>
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
