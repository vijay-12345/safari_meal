@extends('admin.layout')
@section('title', ucfirst($restaurant->name).'::'.trans('admin.add.timing'))
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
						<div class="panel-heading text-center">{{ucfirst($restaurant->name)}} :: {{trans('admin.add.timing')}}</div>
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
																	
							  {!! Form::open(['class'=>'form-horizontal','url' => $prefix.'/timing/add/'.$restaurant->id,'files'=>true]) !!}	
							  	@include('admin.timing.form')
							  	<a href="{{url($prefix.'/timing').'/'.$restaurant->id}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;						  							  								  						  
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