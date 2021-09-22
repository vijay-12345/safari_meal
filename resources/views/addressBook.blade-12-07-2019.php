<?php  
use App\City,App\State,App\Area;
use App\User;
?>
@extends('publicLayout')
@section('title', 'Address Book')
@section('content')
<div class="inner-page-header">
	<div class="container">
	<div class="row">
	<div class="col-md-9">
		<div class="breadcrumbs-cont">
			<p><a href="{{url('/')}}" title="">Home</a> / <a href="{{url('/editprofile')}}" title="">My Profile</a> /<a href="{{url('/addressbook')}}" title="">Address Book</a></p>
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
								</a>
							</li> -->
							<li class="dropdown keep-open">
			         		 	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="flaticon-shape"></i>My Account <span class="flaticon-arrows-1"></span></a>
					          	<ul class="dropdown-menu">
						            <li><a href="{{url('/editprofile')}}"><i class="flaticon-cogwheel"></i>Account Settings
						            	<!-- span class="flaticon-arrows-1"></span> -->
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
							<div class="customadd" style="display:none">Address Added successfully.</div>																																							
							<div class="row">
								<div class="col-md-6">
									<i class="flaticon-gps-1"></i>
										Address Book
								</div>
								<div class="col-md-6">
									<span class="pull-right">
									<a href="{{url('/loadnewaddressbook')}}" data-target="#addnewaddress" data-toggle="modal"><i title ="Add new address"  class="fa fa-plus-circle"></i>
										{{ Lang::get('home.add_new_address') }}
									</a>
									</span>
								</div>
						  </div>
						</div>
						
						@if (session('status'))
						    <div class="alert alert-success">
						        {{ session('status') }}
						    </div>
						@endif					
						<div class="page-data-outer">					
							@foreach($useraddress as $useraddressDetail)							
							<div class="row">
								<div class="col-sm-10">
									<p>
									@if(!empty($useraddressDetail->first_address)){{$useraddressDetail->first_address }} @endif
									@if(!empty($useraddressDetail->second_address)){{','.$useraddressDetail->second_address }} @endif								 
									</p>
									<p>@if(!empty($useraddressDetail->area_id)){{Area::getAreaById($useraddressDetail->area_id)->name }} @endif</p>
									<p>
										@if(!empty($useraddressDetail->city_id)){{City::getCityById($useraddressDetail->city_id)->name }} @endif								
										@if(!empty($useraddressDetail->zip)){{'-'.$useraddressDetail->zip }} @endif								
									</p>
									<p>
										@if(!empty($useraddressDetail->state_id)){{State::getStateById($useraddressDetail->state_id)->state_name }} @endif
									</p>
								</div>
								<div class="col-sm-2 text-right">
									<a href="{{url('/loadupdatedaddressbook?addr='.$useraddressDetail->id)}}" data-toggle="modal" data-target="#ProdAddonaddress{{$useraddressDetail->id}}"><i class="flaticon-interface"></i></a>
									&nbsp&nbsp&nbsp&nbsp&nbsp									
									<a href="#" class="useraddressdelet" rel='{{$useraddressDetail->id.'===='.$useraddressDetail->user_id}}'><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></a>
								</div>
							</div>							
							<div class="modal fade ProdAddonaddresscustom" id="ProdAddonaddress{{$useraddressDetail->id}}" role="dialog">
								<div class="modal-dialog">           
							        <div class="modal-content">
							          							                
							        </div>      
							    </div>
							</div>
							<hr>								
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
