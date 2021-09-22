@extends('admin.layout')
@section('title', trans('admin.restaurant.credential'))
@section('content')
<?php $prefix = \Request::segment(2); ?>
<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
				<?php //echo "testing"; die;?>
			</div>
			<div class="col-md-9">
				<div class="page-data-right">
					<div class="panel panel-default">
						<div class="panel-heading text-center">{{$restaurantObj->name}} :: {{trans('admin.restaurant.credential')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif
							@if($errors->any())
							    <div class="alert alert-danger">
							        @foreach($errors->all() as $error)
							            <p>{{ $error }}</p>
							        @endforeach
							    </div>
							@endif
							@if(!isset($user))																	
							{!! Form::open(['class'=>'form-horizontal','url' => $prefix.'/restaurant/access/'.$restaurantObj->id]) !!}	
							@else
							{!! Form::model($user, ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => $prefix.'/restaurant/access/'.$restaurantObj->id]) !!}	
							@endif
							<br />
							<div class="form-group">
							  <label class="control-label col-sm-2" for="name">{{trans('admin.first.name')}}*:</label>
							  <div class="col-sm-10">
							    {!! Form::text('first_name', null, ['class' => 'form-control','placeholder'=>trans('admin.enter.first.name')]) !!}             
							  </div>
							</div>
							<div class="form-group">

							  <label class="control-label col-sm-2" for="name">{{trans('admin.last.name')}}*:</label>
							  <div class="col-sm-10">
							  {!! Form::text('last_name', null, ['class' => 'form-control','placeholder'=>trans('admin.enter.last.name')]) !!}

							  </div>
							</div>
							<div class="form-group">

							  <label class="control-label col-sm-2" for="name">{{trans('admin.email')}}*:</label>
							  <div class="col-sm-10">
							  {!! Form::text('email', null, ['class' => 'form-control','placeholder'=>trans('admin.enter.email')]) !!}

							  </div>
							</div>
							@if(!isset($user))
							  <div class="form-group">

							    <label class="control-label col-sm-2" for="name">{{trans('admin.password')}}*:</label>
							    <div class="col-sm-10">
							    <input type="password" value="" name="password" autocomplete="off">

							    </div>
							  </div>
							  <div class="form-group">

							    <label class="control-label col-sm-2" for="name">{{trans('admin.confirm.password')}}*:</label>
							    <div class="col-sm-10">
							    {!! Form::password('password_confirmation', null, ['class' => 'form-control','autocomplete'=>'off']) !!}

							    </div>
							  </div>
							@else
							  <div class="form-group">

							    <label class="control-label col-sm-2" for="new_password">{{trans('admin.new.password')}}:</label>
							    <div class="col-sm-10">
							    <input type="password" value="" name="password" autocomplete="off">
							    </div>
							  </div>
							   <div class="form-group">

							    <label class="control-label col-sm-2" for="password_confirmation">{{trans('admin.confirm.password')}}:</label>
							    <div class="col-sm-10">
							    {!! Form::password('password_confirmation', null, ['class' => 'form-control','autocomplete'=>'off']) !!}

							    </div>
							  </div> 
							@endif
						  	<a href="{{url($prefix.'/restaurant')}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;						  							  								  						  
						  	<button type="submit" class="btn btn-primary">{{trans('admin.submit')}}</button>
							{!! Form::close() !!}
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
