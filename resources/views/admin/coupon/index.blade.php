@extends('admin.layout')
@section('title', trans('admin.coupon.list'))
@section('content')
<?php 
use App\Restaurent;
$prefix = \Request::segment(2);
$q = Restaurent::lang()->where('status',1);
$q->where('is_home_cooked',0);
if(Session::get('access.role') != 'admin'){
	$q = $q->whereIn('id',Session::get('access.restaurant_ids'));
}
$restaurants = $q->lists('name','id');
?>
<div class="page-data-cont">
	<div class="container-fluid">
		<div class="row">			
			<div class="col-md-3 side-menu sidebar-menu">
			 	@include('admin.navigation')				
			</div>
			
			<div class="col-md-9">
				<div class="page-data-right">
					<div class="panel panel-default">
						<div class="panel-heading text-center">{{trans('admin.coupon.manager')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => $prefix.'/coupon')) !!}
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

										<input type="text" name="search" value="{{session('filter.search')}}" placeholder="Coupon Code">
										<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
									</td>
									<td>
										<a href="{{url($prefix.'/coupon')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a>
									</td>
								</tr>
							</table>
							{!! Form::close() !!}

							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    	<thead>
					        <tr>					           
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/coupon?sorting={{$filter['sort']}}&amp;field=restaurant.name">{{trans('admin.restaurant.name')}}</a></th>
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/coupon?sorting={{$filter['sort']}}&amp;field=coupon_code">{{trans('admin.coupon.code')}}</a></th>
					             <th><a href="{{URL::to('/')}}/{{$prefix}}/coupon?sorting={{$filter['sort']}}&amp;field=coupon_value">{{trans('admin.coupon.value')}}</a></th>
					           
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/coupon?sorting={{$filter['sort']}}&amp;field=start_date">{{trans('admin.start.date')}}</a></th>
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/coupon?sorting={{$filter['sort']}}&amp;field=end_date">{{trans('admin.end.date')}}</a></th>
					             <th><a href="{{URL::to('/')}}/{{$prefix}}/coupon?sorting={{$filter['sort']}}&amp;field=status">{{trans('admin.status')}}</a></th>
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if($data != null && count($data['data']) > 0)
					    		<?php $i = 0; ?>
						    	@foreach($data['data'] as $v)
						    		<?php $i++; ?>
							        <tr id="list_data{{$v->id}}" @if($i%2==0) style="background-color: #f9f9f9;" @else style="background-color: #FFFFFF;" @endif>					        	
							            <td>{{ ucfirst($v->restaurant_name) }}</td>
							            <td>{{ ucfirst($v->coupon_code) }}</td>
							            <td>{{ ucfirst($v->coupon_value) }}</td>
							            <?php 
							            	$startArr  = $v->start_date ? explode(" ", $v->start_date) : $v->start_date;
							            	$startTime = is_array($startArr) ? $startArr[0] : $v->start_date;
							            	
							            	$endArr  = $v->end_date ? explode(" ", $v->end_date) : $v->end_date;
							            	$endTime = is_array($endArr) ? $endArr[0] : $v->end_date;
							            ?>
							            <td>{{ $startTime }}</td>				            
							            <td>{{ $endTime }}</td>				            
						             	<td>
							             	@if($v->status == 1)
							            		{{trans('admin.active')}}
							            	@else
							            		{{trans('admin.inactive')}}
							            	@endif</td>
							            <td><a href="{{url($prefix.'/coupon').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$v->id}}" data-controller="coupon" style="cursor:pointer;"></span></td>
							        </tr>       
						        @endforeach
					      	@else
					      		<tr><td colspan="8" class="text-center">{{trans('admin.record.not.found')}}</td></tr>
					      	@endif
					        <tr>
					        	@if($data != null)
					        	<td colspan="8">
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