@extends('admin.layout')
@section('title', trans('admin.edit.option'))
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
						<div class="panel-heading text-center">{{trans('admin.edit.option.of')}} <b>{{$group->name}}</div>
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
							  {!! Form::model($option, ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => 'admin/addon_option/update'.'/'.$option->id,'files'=>true]) !!}	
							  	
							  	@include('admin.addon.optionform')							   		 							 									  	
							  	<a href="{{url('admin/addon_option').'/'.$group->id}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;								  								  						  
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
