@extends('admin.layout')
@section('title', 'Edit Page')
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
						<div class="panel-heading text-center">{{trans('Edit Page')}}</div>
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
							  {!! Form::model($page, ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => 'admin/page/update'.'/'.$page->id]) !!}	
							 	@include('admin.page.form')						 									  	
							  	<a href="{{url('admin/page')}}" class="btn btn-primary">{{trans('Back')}}</a>&nbsp;								  								  						  
							  	<button type="submit" class="btn btn-primary">{{trans('Update')}}</button>
							{!! Form::close() !!}
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
