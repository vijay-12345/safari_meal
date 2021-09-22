@extends('admin.layout')
@section('title', trans('admin.addon.group'))
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
						<div class="panel-heading text-center">{{trans('admin.addon.group')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => 'admin/addon_group')) !!}
							<table class="table table-responsive filter-inner-form" cellspacing="0" width="100%">
								<tr>
									<td>
										<select name="paginate_limit" class="paginate_limit">							
											@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
												<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
											@endforeach
										</select>
										<input type="text" placeholder="Name" name="search" value="{{session('filter.search')}}">
										<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
									</td>
									<td>
										<a href="{{url('admin/addon_group')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a>
									</td>
								</tr>
							</table>
							{!! Form::close() !!}

							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>

					            
					            <th><a href="{{URL::to('/')}}/admin/addon_group?sorting={{$filter['sort']}}&amp;field=name">{{trans('admin.name')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/addon_group?sorting={{$filter['sort']}}&amp;field=type">{{trans('admin.type')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/addon_group?sorting={{$filter['sort']}}&amp;field=required">{{trans('admin.required')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/addon_group?sorting={{$filter['sort']}}&amp;field=created_at">{{trans('admin.created')}}</a></th>
					           <th><a href="{{URL::to('/')}}/admin/addon_group?sorting={{$filter['sort']}}&amp;field=status">{{trans('admin.status')}}</a></th>
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if($data != null && count($data['data']) > 0)
					    		
						    	@foreach($data['data'] as $v)
						    		
						        <tr>					        	
						          
						            
						            <td>{{ucfirst($v->name)}}</td>
						            <td>{{$v->type}}</td> 
						            <td>
									@if($v->required == 'Y')
										{{trans('admin.yes')}}
									@else
										{{trans('admin.no')}}
									@endif

						            </td> 	
						            <td>{{$v->created_at}}</td>
						            <td>
									@if($v->status == 1)
									   {{trans('admin.active')}}
									@else
									   {{trans('admin.inactive')}}
									@endif
						            </td>				      		
						             <td><a href="{{url('admin/addon_group').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;<a href="{{url('admin/addon_option').'/'.$v->id}}" class="glyphicon glyphicon-th-list" alt="Option List" title="Option List"></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$v->id}}" data-controller="addon_group" style="cursor:pointer;"></span></td>
						        </tr>    
						      
						        @endforeach
					      	@else
					      		<tr><td colspan="10" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
					      	@endif
					        <tr>
					        	@if($data != null)
					        	<td colspan="10">
					        		<?php echo $data['data']->appends(['sort' => $filter['paginate_sort'],'field'=>$filter['field']])->render(); ?>
					        	</td>
					        	@endif
					        </tr>
					        
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
