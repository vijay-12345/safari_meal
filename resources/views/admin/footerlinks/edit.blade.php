@extends('admin.layout')
@section('title', 'Edit Footer link')
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
						<div class="panel-heading text-center">Edit Footer Link</div>
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
							  {!! Form::model($footerLinks, ['method' => 'post','role' => 'form','class'=>'form-horizontal','url' => 'admin/footer-links/update'.'/'.$footerLinks->id]) !!}	
							 	@include('admin.footerlinks.form')						 									  	
							  	<a href="{{url('admin/footer-links')}}" class="btn btn-primary">Back</a>&nbsp;								  								  						  
							  	<button type="submit" class="btn btn-primary">Update</button>
							{!! Form::close() !!}
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
