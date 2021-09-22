@extends('admin.layout')
@section('title', trans('admin.addon.option'))
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
						<div class="panel-heading text-center">{{$group->name}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							<table class="table table-responsive" cellspacing="0" width="100%">
								<tr>
									<td class="text-left"><a href="{{url('admin/addon_group')}}" class="btn btn-primary btn-md">{{trans('admin.back')}}</a></td>							
									<td class="text-right"><a href="{{url('admin/addon_option/add').'/'.$group->id}}" class="btn btn-primary btn-md">+ {{trans('admin.add.new')}}</a></td>
								</tr>
							</table>

							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>
					            <th>{{trans('admin.group.name')}}</th>
					            <th>{{trans('admin.name')}}</th>
					            <th>{{trans('admin.created')}}</th>
					            <th>{{trans('admin.status')}}</th>
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if(count($data['data']) > 0)
					    		
						    	@foreach($data['data'] as $v)
						    		
						        <tr>					        	
						          
						            
						            <td>
						            	@if(isset($v->group) || !is_null($v->group))
						            		{{$v->group->name}}
						            	@else
						            		N/A
						            	@endif
						            	
						            </td>
						            <td>{{ucfirst($v->item_name)}}</td>
						            <td>{{$v->created_at}}</td>
				            		<td>@if($v->status == 1)
						            		{{trans('admin.active')}}
						            	@else
						            		{{trans('admin.inactive')}}
						            	@endif</td>			            
			   					      		
						             <td><a href="{{url('admin/addon_option').'/edit/'.$v->id}}" class="glyphicon glyphicon-pencil"></a>&nbsp;&nbsp;<a href="{{url('admin/addon_option').'/delete/'.$v->id}}"  onclick="return confirm({{trans('are.you.sure.to.delete.this')}});" class="glyphicon glyphicon-remove"></a></td>
						        </tr>						        
						     
						        @endforeach
					      	@else
					      		<tr><td colspan="10" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
					      	@endif

							<!-- <table class="table table-responsive" cellspacing="0" width="100%">
								<tr>
									<td class="text-left"><a href="{{url('admin/addon_group')}}" class="btn btn-primary btn-md">{{trans('admin.back')}}</a></td>							
									<td class="text-right"><a href="{{url('admin/addon_option/add').'/'.$group->id}}" class="btn btn-primary btn-md">+ {{trans('admin.add.new')}}</a></td>
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
