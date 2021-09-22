<?php 
use App\Image; 
$prefix = \Request::segment(2);
?>
@extends('admin.layout')
@section('title', trans('admin.restaurant.list'))
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
						<div class="panel-heading text-center">{{trans('admin.restaurant.manager')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => $prefix.'/restaurant')) !!}
							<table class="table table-responsive filter-inner-form" cellspacing="0" width="100%">
								<tr>
									@if(Session::get('access.role') == 'admin')
										<td>
											<select name="paginate_limit" class="paginate_limit">							
												@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
													<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
												@endforeach
											</select>
											<input type="text" placeholder="{{trans('admin.name')}}" name="search" value="{{session('filter.search')}}">
											<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
										</td>
									@endif
									
									@if(Session::get('access.role') == 'admin')
										<td><a href="{{url($prefix.'/restaurant')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a></td>
									@endif
								</tr>
							</table>
							{!! Form::close() !!}
							
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">

						    <thead>
						        <tr>
						        	<!-- {{ app('request')->input('sorting') }} -->
						            <th>{{trans('admin.logo')}}</th>
						            <th><a href="{{URL::to('/')}}/{{$prefix}}/restaurant?sorting={{$filter['sort']}}&amp;field=name">{{trans('admin.name')}}</a></th>
						            <th>{{trans('admin.address')}}</th>					 
						            <th><a href="{{URL::to('/')}}/{{$prefix}}/restaurant?sorting={{$filter['sort']}}&amp;field=restaurant.rating">{{trans('admin.rating')}}</a></th>
						            <th><a href="{{URL::to('/')}}/{{$prefix}}/restaurant?sorting={{$filter['sort']}}&amp;field=restaurant.featured">{{trans('admin.featured')}}</a></th>					           
						            <th><a href="{{URL::to('/')}}/{{$prefix}}/restaurant?sorting={{$filter['sort']}}&amp;field=restaurant.status">{{trans('admin.status')}}</a></th>					           
						            <th>{{trans('admin.action')}}</th>
						        </tr>
						    </thead>
					    <tbody>

					    	@if($data != null && count($data['data']) > 0)					    		
						    	@foreach($data['data'] as $v)						    		
						        <tr>					        	
						          	
						            <td>
						            	<?php						         
						            		$image = Image::getImagePath('restaurant');
					            		
					            		if(!is_null($v->logo_location) || !is_null($v->location)){
							            	
								            	$restaurantImage = $v->location; 
												$restaurantLogo  = $v->logo_location;
												// if(!file_exists($restaurantImage)) $restaurantImage = $image['default_image'];
												// if(!file_exists($restaurantLogo)) $restaurantLogo = $image['default_image'];
							            	
						            	}else{
							            							         
							            		$restaurantImage = $restaurantLogo = $image['default_image'];
							            	
							            } ?>
						            		            
						            	<img src="{{$restaurantLogo}}" width="80" alt="logo" style="width: 100px;height: 100px;border-radius: 50%;border: 1px solid #ddd;text-align: center;background-position: 52% 54%!important;background-size: 90px!important; background-repeat: no-repeat!important;">
						            </td>
						            <td>{{ucfirst($v->name)}}</td> 	
					       						            	
						            
						            <td>{{ 'Floor-'.$v->floor.','.$v->street }}</td>						       				            							
						            
						            <td>{{ $v->rating }}</td>
						            <td class="toggleFeatured" data-value="">
						            	@if($v->featured == 1)
						            		{{trans('admin.yes')}}
						            	@else
						            		{{trans('admin.no')}}
						            	@endif
						            </td>						            
						            <td>
						            	@if($v->status == 1)
						            		{{trans('admin.active')}}
						            	@else
						            		{{trans('admin.inactive')}}
						            	@endif


						            </td>
						            <td>

						            	@if( (Session::get('access.role') == 'admin' || Session::get('access.role') == 'restaurant'))
						            	<a href="{{url($prefix.'/restaurant').'/access/'.$v->id}}" class="glyphicon glyphicon-user" <?php if($v->owner_id >0) {?> style="color:#006400;" <?php } ?> alt="{{trans('admin.restaurant.credential')}}"></a>&nbsp;&nbsp;
						            	@endif
						            	<a href="{{url($prefix.'/restaurant').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;
						            	<a href="{{url($prefix.'/timing').'/'.$v->id}}" class="glyphicon glyphicon-time" alt="Open Timing" title="Open Timing"></a>&nbsp;&nbsp;
						            	@if(Session::get('access.role') == 'admin')
						            		<span class="glyphicon glyphicon-remove delete-action" data-id="{{$v->id}}" data-controller="restaurant" style="cursor:pointer;"></span>
						            	@endif
						            </td>
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