@extends('admin.layout')
@section('title', 'Review List')
@section('content')
<?php 
use App\Restaurent;
$prefix = \Request::segment(2);
$q = Restaurent::lang()->where('status',1);
$q->where('is_home_cooked',1);
if(Session::get('access.role') == 'manager'){
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
						<div class="panel-heading text-center">{{trans('admin.review.manager')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => $prefix.'/homecookedreview')) !!}
							<table class="table table-responsive filter-inner-form" cellspacing="0" width="100%">
								<tr>
									<td>								
										<select name="paginate_limit" class="paginate_limit">							
											@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
												<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
											@endforeach
										</select>
										{!! Form::select('restaurant_id',['All'=>trans('admin.restaurant.all')]+$restaurants,session('filter.restaurant_id'),['class'=>'restaurant']) !!}

										{!! Form::select('rating',['All'=>trans('admin.rating.all'),'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5],session('filter.rating'),['class'=>'rating']) !!}
										<input type="text" name="search" value="{{session('filter.search')}}" placeholder="{{trans('admin.user.name')}}">
										<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
									</td>
									<td>
										<a href="{{url($prefix.'/homecookedreview')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a>
									</td>
								</tr>
							</table>
							{!! Form::close() !!}
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>					           
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/homecookedreview?sorting={{$filter['sort']}}&amp;field=restaurant.name">{{trans('admin.restaurant')}}</a></th>
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/homecookedreview?sorting={{$filter['sort']}}&amp;field=user.first_name">{{trans('admin.customer')}}</a></th>
					             <th><a href="{{URL::to('/')}}/{{$prefix}}/homecookedreview?sorting={{$filter['sort']}}&amp;field=review.review">{{trans('admin.review')}}</a></th>
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/homecookedreview?sorting={{$filter['sort']}}&amp;field=review.rating">{{trans('admin.rating')}}</a></th>
					            <th><a href="{{URL::to('/')}}/{{$prefix}}/homecookedreview?sorting={{$filter['sort']}}&amp;field=review.date">{{trans('admin.date')}}</a></th>
					            
					             
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
						            <td>{{ ucfirst($v->user_fname).' '.ucfirst($v->user_lname) }}</td>
						            <td>{{ $v->review }}</td>
						            <td>{{ $v->rating }}</td>
						            <td>{{ date('d-m-Y',strtotime($v->date)) }}</td>	            
						 

						            						            							
						            <td><a href="{{url($prefix.'/homecookedreview').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;<span class="glyphicon glyphicon-remove delete-action" data-id="{{$v->id}}" data-controller="homecookedreview" style="cursor:pointer;"></span></td>
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