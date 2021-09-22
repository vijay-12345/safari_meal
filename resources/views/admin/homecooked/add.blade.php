@extends('admin.layout')

@section('title', trans('admin.add.restaurant'))

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
						<div class="panel-heading text-center">{{trans('admin.add.restaurant')}}</div>
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
																	
							{!! Form::open(['class'=>'form-horizontal','url' => $prefix.'/homecooked/add','files'=>true]) !!}	
							  	@include('admin.homecooked.form')
    		
				    			@if(!empty($data['referer']))
				    				{!! Form::hidden('referer',$data['referer']) !!}
				    				<a href="{{url($prefix.'/'.$data['referer'])}}" class="btn btn-primary">{{trans('admin.back')}}</a> &nbsp;
				    			@else
				    				<a href="{{url($prefix.'/homecooked')}}" class="btn btn-primary">{{trans('admin.back')}}</a> &nbsp;
				    			@endif
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
