<?php use App\Image; ?>
@extends('admin.layout')
@section('title', trans('admin.menu.list'))
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
						<div class="panel-heading">{{trans('admin.menu.list')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => 'admin/menu')) !!}
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
										<a href="{{url('admin/menu')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a>
									</td>
								</tr>
							</table>
							{!! Form::close() !!}
							
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>					           
					            <th>{{trans('admin.image')}}</th>
					            <th><a href="{{URL::to('/')}}/admin/menu?sorting={{$filter['sort']}}&amp;field=name">{{trans('admin.name')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/menu?sorting={{$filter['sort']}}&amp;field=created_at">{{trans('admin.created')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/menu?sorting={{$filter['sort']}}&amp;field=status">{{trans('admin.status')}}</a></th>
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if(count($data['data']) > 0)
					    		<?php $i = 0; ?>
						    	@foreach($data['data'] as $v)
						    		<?php $i++; ?>
						        <tr id="list_data{{$v->id}}" @if($i%2==0) style="background-color: #f9f9f9;" @else style="background-color: #FFFFFF;" @endif>					        	
				            		<td>
							         	@if(!is_null($v->image))
						            	
						            	<?php 
						            	$menuImage = $v->image->location; 
										
						            	?>
						            	@else
						            	<?php 
						            		 $image = Image::getImagePath('menu');
						            		 $menuImage = $image['default_image'];						            		 
						            	?>
						            	@endif	            
						            	<img src="{{$menuImage}}" width="80" alt="logo">
						            </td>	
						            <td>{{ ucfirst($v->name) }}</td>
						            <td>{{ ucfirst($v->created_at) }}</td>
						            <td>@if($v->status == 1)
						            		{{trans('admin.active')}}
						            	@else
						            		{{trans('admin.inactive')}}
						            	@endif</td>								
						            <td><span class="glyphicon glyphicon-plus-sign view-action" data-id="{{$v->id}}" style="cursor:pointer;"></span>&nbsp;&nbsp;<a href="{{url('admin/menu').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$v->id}}" data-controller="menu" style="cursor:pointer;"></span></td>
						        </tr>						        
						        <tr style="display:none;" id="view_data{{$v->id}}">
						        	<td colspan="8">
						        		<table class="table-responsive" cellspacing="0" width="100%">
						        			<tr>
						        				<td><strong>{{trans('admin.menu.id')}} :</strong> {{ $v->id }}</td>						        				
						        				<td><strong>{{trans('admin.name')}} :</strong> {{ucfirst($v->name)}}</td>
						        				<td><strong>{{trans('admin.status')}}:</strong>
													@if($v->status == 1)
									            		{{trans('admin.active')}}
									            	@else
									            		{{trans('admin.inactive')}}
									            	@endif
						        				 </td>
						         			</tr>						       
						        			<tr>
						        				<td><strong>{{trans('admin.created')}} :</strong> {{ $v->created_at }}</td>						        				
						        				<td><strong>{{trans('admin.updated')}} :</strong> {{ $v->updated_at }}</td>
						        				<td>&nbsp;</td>
						        				
						         			</tr>
						        			         					          			       			
						        		</table>
						        	</td>	
						        </tr>
						        @endforeach
					      	@else
					      		<tr><td colspan="8" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
					      	@endif
					        <tr>
					        	<td colspan="8">
					        		<?php echo $data['data']->appends(['sort' => $filter['paginate_sort'],'field'=>$filter['field']])->render(); ?>
					        	</td>
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
