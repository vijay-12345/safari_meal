<?php 
use App\Image,App\Restaurent; 
$prefix = \Request::segment(2);
$q = Restaurent::lang()->where('status',1);
if(Session::get('access.role') != 'admin'){
	$q = $q->whereIn('id',Session::get('access.restaurant_ids'));
}
$restaurants = $q->lists('name','id');
?>

@extends('admin.layout')
@section('title', trans('admin.product.list'))
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
						<div class="panel-heading text-center">{{trans('admin.product.manager')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => $prefix.'/product')) !!}
							<table class="table table-responsive filter-inner-form" cellspacing="0" width="100%">
								<tr>
									<td>
										<select name="paginate_limit" class="paginate_limit">							
											@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
												<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
											@endforeach
										</select>

										@if(Session::get('access.role') == 'admin')
											{!! Form::select('restaurant_id',['All'=>trans('admin.restaurant.all')]+$restaurants,session('filter.restaurant_id'),['class'=>'restaurant']) !!}
										@endif
										
										<input type="text" placeholder="{{trans('admin.name')}}" name="search" value="{{session('filter.search')}}">
										<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
									</td>
									<td>
										<a href="{{url($prefix.'/product')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}
										</a>
									</td>
								</tr>
							</table>

							{!! Form::close() !!}
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>
					        	<th><a href="{{URL::to('/')}}/{{$prefix}}/product?sorting={{$filter['sort']}}&amp;field=restaurant.name">{{trans('admin.restaurant')}}</a></th>
					            <th>{{trans('admin.image')}}</th>

					            <th><a href="{{URL::to('/')}}/{{$prefix}}/product?sorting={{$filter['sort']}}&amp;field=product.name">{{trans('admin.name')}}</a></th>
					           
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/product?sorting={{$filter['sort']}}&amp;field=product.created_at">{{trans('admin.created')}}</a></th>
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/product?sorting={{$filter['sort']}}&amp;field=product.status">{{trans('admin.status')}}</a></th>
					            <th>{{trans('admin.cost')}}</th>        
					           
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if(count($data['data']) > 0)
					    		<?php $i = 0; ?>
						    	@foreach($data['data'] as $v)
						    		<?php $i++; ?>
						        <tr id="list_data{{$v->id}}" @if($i%2==0) style="background-color: #f9f9f9;" @else style="background-color: #FFFFFF;" @endif>				        	
						          	<td>{{ucfirst($v->restaurant_name)}}</td> 
						            <td>
						            	<?php						         
						            		$image = Image::getImagePath('restaurant');
					            		?>

							         	@if(!is_null($v->image))
							            	<?php 
							            		$restaurantImage = $v->image->location;
							            		if(!file_exists($restaurantImage)) $restaurantImage = $image['default_image'];
							            	?>
						            	@else
							            	<?php $restaurantImage = $image['default_image']; ?>
						            	@endif            
						            	<img src="{{$restaurantImage}}" width="80" alt="logo">
						            </td>
		            					            
						            <td>{{ucfirst($v->name)}}</td>  	
					      				
						           <td>{{$v->created_at}}</td>
												             <td>
						             	@if($v->status == 1)
						            		{{trans('admin.active')}}
						            	@else
						            		{{trans('admin.inactive')}}
						            	@endif</td>	           
						            <td>{{format($v->cost)}}</td>
					             	<td>
					             		<span class="glyphicon glyphicon-plus-sign view-action" data-id="{{$v->id}}" style="cursor:pointer;"></span>&nbsp;&nbsp;<a href="{{url($prefix.'/product').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$v->id}}" data-controller="product" style="cursor:pointer;"></span></td>
						        </tr>				        
						        <tr style="display:none;" id="view_data{{$v->id}}">
						        	<td colspan="10">
						        		<table class="table-responsive" cellspacing="0" width="100%">
						        			<tr>
						        				<td><strong>{{trans('admin.product.id')}} :</strong> {{ $v->id }}</td>						        				
						        				<td><strong>{{trans('admin.name')}} :</strong> {{ucfirst($v->name)}}</td>
						         			</tr>
						        		</table>
						        	</td>	
						        </tr>
						        @endforeach
					      	@else
					      		<tr><td colspan="10" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
					      	@endif
					        <tr>
					        	<td colspan="10">
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