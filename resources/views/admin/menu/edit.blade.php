@extends('admin.layout')
@section('title', trans('admin.edit.menu'))
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
						<div class="panel-heading text-center">{{trans('admin.edit.menu')}}</div>
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

							<!-- vijayanand -->
							<?php $roleid = Auth::user()->role_id;  ?>
					        @if($roleid == 1)													
							  {!! Form::model($menu, ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => 'admin/menu/update'.'/'.$menu->id,'files'=>true]) !!}	
							  	@include('admin.menu.form')	 							 									  	
							  	<a href="{{url('admin/menu')}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;								  								  						  
							  	<button type="submit" class="btn btn-primary">{{trans('admin.update')}}</button>
							{!! Form::close() !!}
							@endif

							@if($roleid == 6)													
							  {!! Form::model($menu, ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => 'restaurant/menu/update'.'/'.$menu->id,'files'=>true]) !!}	
							  	@include('admin.menu.form')	 							 									  	
							  	<a href="{{url('restaurant/menu')}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;								  								  						  
							  	<button type="submit" class="btn btn-primary">{{trans('admin.update')}}</button>
							{!! Form::close() !!}
							@endif



						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
