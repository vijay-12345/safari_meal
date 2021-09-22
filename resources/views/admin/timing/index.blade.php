@extends('admin.layout')
@section('title', $restaurant->name. trans('admin.timings'))
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
						<div class="panel-heading text-center">{{$restaurant->name}} {{trans('admin.timings')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif			
							<table class="table table-responsive" cellspacing="0" width="100%">
								
								<tr>
									<td class="text-left"><a href="{{url($prefix.'/restaurant')}}" class="btn btn-primary btn-md">{{trans('admin.back')}}</a></td>							
									<td class="text-right"><a href="{{url($prefix.'/timing/add').'/'.$restaurant->id}}" class="btn btn-primary btn-md">+ {{trans('admin.add.new')}}</a></td>
								</tr>
							</table>
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>					        	
					            <th>{{trans('admin.open.time')}}</th>
					            <th>{{trans('admin.closing.time')}}</th>
					            <th>{{trans('admin.weekday')}}</th>
					            <th>{{trans('admin.delivery.start')}}</th>
					            <th>{{trans('admin.delivery.end')}}</th>
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if(count($data['data']) > 0)
					    		
						    	@foreach($data['data'] as $v)
						    		
						        <tr>					        	
						          	
						            <td>{{$v->open}}</td>
						            <td>{{ucfirst($v->closing)}}</td>  	
					      			<td>
					      				@foreach(\Config::get('constants.timing.options') as $key=>$w)
					      					@if($key == $v->weekday)
					      						{{ $w }}
					      					@endif
					      				@endforeach
					      				

					      			</td>	
						           
						            <td>{{ $v->delivery_start }}</td>	
						            <td>{{ $v->delivery_end }}</td>	
						             <td><a href="{{url($prefix.'/timing').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;<a href="{{url($prefix.'/timing/delete').'/'.$v->id}}" onclick="return confirm('Are you sure you want to delete this?');" class="glyphicon glyphicon-remove"></a></td>
						        </tr>	        

						        @endforeach
					      	@else
					      		<tr><td colspan="10" class="text-center">{{trans('admin.not.found')}}</td></tr>
					      	@endif
					      	
							<!-- <table class="table table-responsive" cellspacing="0" width="100%">
								
								<tr>
									<td class="text-left"><a href="{{url($prefix.'/restaurant')}}" class="btn btn-primary btn-md">{{trans('admin.back')}}</a></td>							
									<td class="text-right"><a href="{{url($prefix.'/timing/add').'/'.$restaurant->id}}" class="btn btn-primary btn-md">+{{trans('admin.add.new')}}</a></td>
								</tr>
							</table> -->
					        
					    </tbody>
						</table>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
