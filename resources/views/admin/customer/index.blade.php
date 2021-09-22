<?php use App\Image; ?>
@extends('admin.layout')
@section('title', trans('admin.customer.list'))
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
						<div class="panel-heading">{{trans('admin.customer.manager')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => 'admin/customer')) !!}
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
										<a href="{{url('admin/customer')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a>
									</td>
								</tr>
							</table>
							{!! Form::close() !!}
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>					           
					            <th>{{trans('admin.image')}}</th>
					            <th><a href="{{URL::to('/')}}/admin/customer?sorting={{$filter['sort']}}&amp;field=first_name">{{trans('admin.name')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/customer?sorting={{$filter['sort']}}&amp;field=email">{{trans('admin.email')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/customer?sorting={{$filter['sort']}}&amp;field=contact_number">{{trans('admin.contact.number')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/customer?sorting={{$filter['sort']}}&amp;field=created_at">{{trans('admin.created')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/customer?sorting={{$filter['sort']}}&amp;field=status">{{trans('admin.status')}}</a></th>
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if($data != null && count($data['user_data']) > 0)
					    		<?php $i = 0; ?>
						    	@foreach($data['user_data'] as $user)
						    		<?php $i++; ?>
						        <tr id="list_data{{$user->id}}" @if($i%2==0) style="background-color: #f9f9f9;" @else style="background-color: #FFFFFF;" @endif>					        	
									<td>
							         	@if($user->profile_image !='')
						            	<img src="{{$user->profile_image}}" alt="logo" width="50">						            	
						            	@else
						            		 <span class="glyphicon glyphicon-user"></span>
						            	@endif	            
						            	
						            </td>						            
						            <td>{{ ucfirst($user->first_name).' '.ucfirst($user->last_name) }}</td>
						            <td>{{ $user->email }}</td>
						            <td>{{ $user->contact_number }}</td>
						            <td>{{ $user->created_at }}</td>	
									<td>
						             	@if($user->status == 1)
						            		{{trans('admin.active')}}
						            	@else
						            		{{trans('admin.inactive')}}
						            	@endif</td>		            							
						            <td><span class="glyphicon glyphicon-plus-sign view-action md-icon-link" data-id="{{$user->id}}" style="cursor:pointer;"></span>&nbsp;&nbsp;<a href="{{url('admin/customer').'/edit/'.$user->id}}"><span class="glyphicon glyphicon-pencil md-icon-link" style="cursor:pointer;" data-id="{{$user->id}}"></span></a>&nbsp;&nbsp;<a href="{{url('admin/address').'/'.$user->id}}" class="fa fa-map-marker lg-icon-link" alt="{{trans('admin.address.list')}}" title="{{trans('admin.address.list')}}"></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action md-icon-link" data-id="{{$user->id}}" data-controller="customer" style="cursor:pointer;"></span></td>
						        </tr>						        
						        <tr style="display:none;" id="view_data{{$user->id}}">
						        	<td colspan="8">
						        		<table class="table-responsive" cellspacing="0" width="100%">
						        			<tr>
						        				<td><strong>{{trans('admin.customer.id')}} :</strong> {{ $user->id }}</td>						        				
						        				<td><strong>{{trans('admin.name')}} :</strong> {{ucfirst($user->first_name).' '.ucfirst($user->last_name)}}</td>
						         			</tr>
						         			<tr>	
						         				<td><strong>{{trans('admin.contact')}} :</strong> {{ $user->contact_number }}</td>						        				
						        				<td><strong>{{trans('admin.email')}} :</strong> {{ $user->email }}</td>						        			       			
						        			</tr>
						          			<tr>	
						         				<td><strong>{{trans('admin.created')}} :</strong> {{ $user->created_at }}</td>						        				
						        				<td><strong>{{trans('admin.status')}} :</strong>
						        				@if($user->status == 1)
								            		{{trans('admin.active')}}
								            	@else
								            		{{trans('admin.inactive')}}
								            	@endif</td>						        			       			
						        			</tr>
						          			       			
						        		</table>
						        	</td>	
						        </tr>
						        @endforeach
					      	@else
					      		<tr><td colspan="8" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
					      	@endif
					        <tr>
					        	@if($data != null)
					        	<td colspan="8">
					        		<?php echo $data['user_data']->appends(['sort' => $filter['paginate_sort'],'field'=>$filter['field']])->render(); ?>
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
