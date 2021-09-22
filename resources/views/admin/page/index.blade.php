@extends('admin.layout')
@section('title', trans('admin.cms.list'))
@section('content')
<?php 
use App\Restaurent,App\User;
$restaurants = Restaurent::lang()->where('status',1)->lists('name','id');
$customers = User::where(['status'=>1,'role_id'=>Config::get('constants.user.customer')])->lists('email','id');
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
						<div class="panel-heading text-center">{{trans('admin.cms.manager')}}</div>
						<div class="panel-body">
							@if(Session::has('flash_message'))
							    <div class="alert alert-success">
							        {{ Session::get('flash_message') }}	   
							    </div>
							@endif

							{!! Form::open(array('role' => 'form','class'=>'filter-form','url' => 'admin/page')) !!}
							<table class="table table-responsive filter-inner-form" cellspacing="0" width="100%">
								<tr>									
									<td>
										<select name="paginate_limit" class="paginate_limit">							
											@foreach(Config::get('constants.paginate_limit_option') as $limitOption))
												<option value="{{$limitOption}}" @if(session('filter.paginate_limit')==$limitOption) selected @endif>{{$limitOption}}</option>
											@endforeach
										</select>
										<input type="text" placeholder="Title Name" name="search" value="{{session('filter.search')}}">
										<button type="submit" class="btn btn-primary" value="{{trans('admin.search')}}">{{trans('admin.search')}}</button>
									</td>
									<td>
										<a href="{{url('admin/page')}}/add" class="btn btn-primary btn-md">+ {{trans('admin.add')}}</a>
									</td>
								</tr>
							</table>
							{!! Form::close() !!}
							<table id="table" class="table table-striped dashboard-order" cellspacing="0" width="100%">
					    <thead>
					        <tr>					           
					            <th><a href="{{URL::to('/')}}/admin/page?sorting={{$filter['sort']}}&amp;field=title">{{trans('admin.title')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/page?sorting={{$filter['sort']}}&amp;field=description">{{trans('admin.description')}}</a></th>
					             <th><a href="{{URL::to('/')}}/admin/page?sorting={{$filter['sort']}}&amp;field=page_urlalias">{{trans('admin.urlalias')}}</a></th>
					            <th><a href="{{URL::to('/')}}/admin/page?sorting={{$filter['sort']}}&amp;field=status">{{trans('admin.status')}}</a></th>
				
					            <th>{{trans('admin.action')}}</th>
					        </tr>
					    </thead>
					    <tbody>

					    	@if($data != null && count($data['data']) > 0)
					    		
						    	@foreach($data['data'] as $v)						    		
						        <tr>					        	
						            <td>{{ ucfirst($v->title) }}</td>						           
						            <td>{{ str_limit(strip_tags($v->description),200,'...') }}</td>
						            <td>{{ $v->page_urlalias }}</td>
						            <td>@if($v->status == 1)
						            		{{trans('admin.active')}}
						            	@else
						            		{{trans('admin.inactive')}}
						            	@endif
						            	
						            </td>            							
						            <td>
						            	<a href="{{url('admin/page').'/edit/'.$v->id}}"><span class="glyphicon glyphicon-pencil md-icon-link" style="cursor:pointer;" data-id="{{$v->id}}"></span></a>&nbsp;&nbsp;
						            	@if(!in_array($v->page_urlalias,Config::get('constants.page.nodelete')))
						            	<span class="glyphicon glyphicon-remove delete-action md-icon-link" data-id="{{$v->id}}" data-controller="page" style="cursor:pointer;"></span>
						            	@else
						            	
						            	@endif
						            </td>
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
