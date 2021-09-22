<?php $prefix = \Request::segment(2); ?>
<?php use App\Restaurent; ?>
<header>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-5">
				<a href="{{url($prefix.'/dashboard')}}" title="" class="logo"><img src="{{ asset('images/admin-logo.png') }}" alt=""></a>
			</div>

			<!-- vijayanand -->
			<div class="col-sm-5">
				<?php $roleid = Auth::user()->role_id;  ?>
				<?php $id = Auth::user()->id; ?>
		    	<?php $res = Restaurent::where('owner_id',$id)->first(); ?>
		    	<?php $resname = $res['name']; ?>
				@if($roleid == 6)
				<h1 style="color:white;">{{$resname}}</h1>
				@endif
			</div>
			<div class="col-sm-7">
				<div class="top-link text-right">
					<ul class="list-inline">

						@include('admin.notification_bell')
						
						<!-- comment multi language functionality -->
						<!-- <li>
							<div class="dropdown">
							  	<a id="dLabel" data-target="#" href="{{url($prefix.'/dashboard')}}" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							  		<i class="flaticon-earth"></i>
							    	{{trans('admin.language')}}
							    	<span class="flaticon-arrows"></span>
							  	</a>
								
							  	<ul class="dropdown-menu" aria-labelledby="dLabel">
							  		@foreach(Config::get('app.alt_langs') as $lang)
							  			<li>
							  				<a href="{{'/'.$lang.'/'.$prefix.'/dashboard'}}" class="" title="">{{Config::get('constants.language_map.'.$lang)}}
							  				</a>
							  			</li>
							  		@endforeach
							  	</ul>
							</div>
						</li> -->

						<li>
							<div class="dropdown">
							  <a id="dLabel" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							  	<i class="flaticon-round"></i>
							    {{ucfirst(Auth::user()->first_name).' '.ucfirst(Auth::user()->last_name)}}
							    <span class="flaticon-arrows"></span>
							  </a>
							  
							  <ul class="dropdown-menu" aria-labelledby="dLabel">
							  	<!--<li><a href="#" class="" title="" onclick="alert('Under Development');">Setting</a></li>-->
							    <li><a href="{{url($prefix.'/logout')}}" class="" title="">Logout</a></li>
							  </ul>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</header>