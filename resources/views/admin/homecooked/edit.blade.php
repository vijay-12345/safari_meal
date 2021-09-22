@extends('admin.layout')
@section('title', trans('admin.edit.restaurant'))
@section('content')
<?php $prefix = \Request::segment(2); ?>
<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
				
			</div>
			<div class="col-md-9">
				<div class="page-data-right">
					<div class="panel panel-default">
						<div class="panel-heading text-center">{{trans('admin.edit.restaurant')}}</div>
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
							  {!! Form::model($restaurant, ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => $prefix.'/homecooked/update'.'/'.$restaurant->id,'files'=>true]) !!}	
							  	
							  	@include('admin.homecooked.form')							   		 							 									  	
							  	<a href="{{url($prefix.'/homecooked')}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;								  								  						  
							  	
							  	<button type="submit" class="btn btn-primary">{{trans('admin.update')}}</button>						  	

								
							{!! Form::close() !!}
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
