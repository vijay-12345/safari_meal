<?php
use App\City;
use App\State;
use App\Country,App\Area;
?>
@extends('admin.layout')
@section('title', trans('admin.address.list'))
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
						<div class="panel-heading text-center">{{trans('admin.address.list.of')}} <b>{{ucfirst($data['user']->first_name).' '.ucfirst($data['user']->last_name)}}</b></div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif			
							
							<table class="table table-responsive" cellspacing="0" width="100%">
								<tr>
									<td class="text-left"><a href="#" onclick="window.history.back()" class="btn btn-primary btn-md">{{trans('admin.back')}}</a></td>							
									<td class="text-right"><a href="{{url($prefix.'/address-add').'/'.$data['user']->id}}" class="btn btn-primary btn-md">+ {{trans('admin.add.new')}}</a></td>
								</tr>
							</table>
							
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
						    <thead>
						        <tr>					           
						            <th>{{trans('admin.first.address')}}</th>
						            <th>{{trans('admin.second.address')}}</th>
						            <th>{{trans('admin.area')}}</th>
						            <th>{{trans('admin.zipcode')}}</th>
						            <th>{{trans('admin.city')}}</th>
						            <th>{{trans('admin.state')}}</th>
						            <th>{{trans('admin.country')}}</th>	            					            
						            <th>{{trans('admin.action')}}</th>
						        </tr>
						    </thead>
					    <tbody>

					    	@if(count($data['data']) > 0)
					    		
						    	@foreach($data['data'] as $v)
						    		
						        <tr>					        	
						            <td>{{$v->first_address}}</td>			
						            <td>{{$v->second_address}}</td>
						            <td>
						            {{empty($v->area)?$v->area->name:'N/A'}}						      	
						            </td>		            
						            <td>{{$v->zip}}</td>	
						            <td>
						            {{empty($v->city)?$v->city->name:'N/A'}}						      	
						            </td>
						            <td>
						            {{empty($v->state)?$v->state->state_name:'N/A'}}	
						      	    </td>
						            <td>
						            {{empty($v->country)?$v->country->country_name:'N/A'}}						      		
						            </td>				            							
						            <td><a href="{{url($prefix.'/address-edit').'/'.$v->id}}" class="glyphicon glyphicon-pencil md-icon-link"></a>&nbsp;&nbsp;<a href="{{url($prefix.'/address-delete').'/'.$v->id}}" onclick="return confirm(".trans('admin.are.you.sure.to.delete.this').");" class="glyphicon glyphicon-remove md-icon-link"></a></td>
						        </tr>		        
						        
						        @endforeach
					      	@else
					      		<tr><td colspan="8" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
					      	@endif
					    </tbody>
						</table>
						
						<!-- <table class="table table-responsive" cellspacing="0" width="100%">
							<tr>
								<td class="text-left"><a href="#" onclick="window.history.back()" class="btn btn-primary btn-md">{{trans('admin.back')}}</a></td>							
								<td class="text-right"><a href="{{url($prefix.'/address-add').'/'.$data['user']->id}}" class="btn btn-primary btn-md">+ {{trans('admin.add.new')}}</a></td>
							</tr>
						</table> -->

						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
