<?php  
use App\City, App\Cuisine, App\Restaurent;
if(Auth::check() && Auth::user()->role_id != \Config::get('constants.user.customer') ){
	Auth::logout();
}
?>

@extends('publicLayout')

@section('title', Lang::get('common.title'))

@section('content')

<?php
	$cities = City::lang()->where('status',1)->get();	
	$cuisines = Cuisine::lang()->where('status',1)->lists('name','id');
	$featuredRes = Restaurent::lang()->where(['status'=>1,'featured'=>1])->get();
?>

<div class="home-banner" style="background: url('{{url('/images/banner.png')}}');">
	<div class="banner-layer"></div>
	<div class="banner-data-outer">
		<div class="container">
			<div class="banner-data">
				<h2>{{Lang::get('home.Order.online.delicious.food.delivery.in')}}</h2>
				<h4>{{Lang::get('home.Discover.all.the.Librevilles.restaurant.that.deliver.to.your.doorstep')}}</h4>
			</div>
			<div class="food-order-cont">
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
						<div class="search-from">
							{!! Form::open(array('url' => 'restaurentlist','method'=>'get')) !!}
							<div class="sform-outer">
								<div class="row">
									<div class="col-sm-3 border-right">
										<div class="field-cont location-field">
											<select name="searchcity" class="search-field" id="searchcity">
												<option value="">{{Lang::get('home.Select City')}}</option>
												@foreach($cities as $city)
												<option data-option="{{$city->id}}" value="{{$city->name}}">{{$city->name}}</option>
												@endforeach	
											</select>
											<i class="flaticon-location"></i>
										</div>
									</div>
									<div class="col-sm-9">
										<div class="field-cont food-field">
											<input disabled name="searcharea" type="text" class="typeahead search-field" placeholder="{{Lang::get('home.Enter an area')}}">	
											<span class="flaticon-arrows" id='flaticon-arrows'></span>									
											<i class="flaticon-interface"></i>
											<button disabled="disabled" type="button" class="btn btn-srch">{{Lang::get('home.Show Restaurants')}}</button>
											<!--span class="customsearchbutton">{{Lang::get('home.Show Restaurants')}}</span-->
										</div>
									</div>
								</div>
							</div>
							{!! Form::close() !!}
						</div> <!--/search form-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--/home-banner-->

<section id="steps">
	<div class="container">
		<h3 class="blue-text uppercase">{{Lang::get('home.Get.your.favourite.food.in.4.simple.steps')}}</h3>
		<div class="row">
			<div class="col-md-3">
				<div class="step-box text-center">
					<div class="icon">
						<img src="images/icon1.png" alt="">
					</div>
					<h6>{{Lang::get('home.Step')}} 1</h6>
					<h4>{{Lang::get('home.Search')}}</h4>
					<p>{{Lang::get('home.Find.restaurants.that.deliver.to.you.by.entering.your.address')}}</p>
				</div>
			</div>
			<div class="col-md-3">
				<div class="step-box text-center">
					<div class="icon">
						<img src="images/icon2.png" alt="">
					</div>
					<h6>{{Lang::get('home.Step')}} 2</h6>
					<h4>{{Lang::get('home.Choose')}}</h4>
					<p>{{Lang::get('home.Browse.hundreds.of.menus.to.find.the.food.you.like')}}</p>
				</div>
			</div>
			<div class="col-md-3">
				<div class="step-box text-center">
					<div class="icon">
						<img src="images/icon3.png" alt="">
					</div>
					<h6>{{Lang::get('home.Step')}} 3</h6>
					<h4>{{Lang::get('home.Pay Bill')}}</h4>
					<p>{{Lang::get('home.Pay.fast.&amp;.secure.online.or.on.delivery')}}</p>
				</div>
			</div>
			<div class="col-md-3">
				<div class="step-box text-center">
					<div class="icon">
						<img src="images/icon4.png" alt="">
					</div>
					<h6>{{Lang::get('home.Step')}} 4</h6>
					<h4>{{Lang::get('home.Enjoy')}}</h4>
					<p>{{Lang::get('home.Food.is.prepared.&amp;.delivered.to.your.door')}}</p>
				</div>
			</div>
		</div>
	</div>
</section><!--/section-->

@if(!empty($featuredRes))
	<section class="blue-bg">
		<div class="container">
			<h3 class="white-color uppercase">{{Lang::get('home.Suggested.Restaurants')}}</h3>
			<div class="restro-list-home text-center">
				<ul class="list-inline">
					@foreach($featuredRes as $key=>$v)
					<li>
						<a class="sg-restro-single" href="{{url('restaurentdetail/'.$v->restaurent_urlalias)}}" title="{{$v->name}}">					
							@if( isset($v->image->logo_location) )
								<span class="sr-logo"><img src="{{$v->image->logo_location}}" height="80" class="responsive"></span>
							@endif
							@if( isset($v->image->logo_location) )
							<!--<span style="background: url({{$v->image->location}});" class="sr-img responsive"></span>-->					
							@endif
						</a>
					</li>
					@endforeach
					<?php //echo '<pre>';print_r($featuredRes->toArray());die;?>
				</ul>
			</div>
		</div>
	</section>
@endif

<section class="bg cuisines-list" style="background: url('images/cuisines-bg.png');">
	<div class="layer"></div>
	<div class="container">
		<div class="clist-data">
			<h3 class="white-color">{{Lang::get('home.Cuisines')}}</h3>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="list-outer">
						<ul class="clearfix">
							@foreach($cuisines as $key=>$cuisine)							
								<li><a href="{{url('restaurentlist/?cuisines[]='.$cuisine.'&cousinlimit=5')}}">{{ucfirst($cuisine)}}</a></li>
							@endforeach							
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="app-sec">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="app-sec-outer clearfix">
					<div class="row">
						<div class="col-sm-6">
							<div class="mobile-app-img"><img src="images/mobile-app-img.png" class="img-responsive"></div>
						</div>
						<div class="col-sm-6">
							<div class="app-sec-data">
								<h4>{{Lang::get('home.Foodbox.in.your.pocket!')}}</h4>
								<h5>{{Lang::get('home.Get.our.app,.it.the.fastest.way.to.order.food.on.the.go.')}}</h5>
								<div class="app-btn">
									<a href="#" title=""><img src="images/android-app-btn.png" alt=""></a>
									<a href="#" title=""><img src="images/app-store.png" alt=""></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="top-border">
	<div class="container">
		<h3 class="margin-bottom10">{{Lang::get('home.Subscribe.to.our.newsletter')}}</h3>
		<ul class="list-style list-inline text-center">
			<li>{{Lang::get('home.Dont.miss.out.on.our.great.offers')}}</li>
			<li>{{Lang::get('home.Recieve.deals.from.all.our.top.restaurants.via.e-mail')}}</li>
		</ul>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="subscribe-msg">

				</div>					
				<div class="email-subscribe-cont">
					{!! Form::open(array('role' => 'form','class'=>'subscribe','url' => 'subscribe')) !!}
						<div class="es-field-cont">
							<input type="input" name="email" placeholder="{{Lang::get('home.Enter.your.email')}}" class="es-field">
							<i class="flaticon-letter"></i>
							<button type="button" class="btn btn-es">{{Lang::get('home.Subscribe.Now')}}</button>							
						</div>
						<div class="term-cont">
							<input type="checkbox" name="policy" id="checkboxG1" class="css-checkbox" checked="checked" />
							<label for="checkboxG1" class="css-label">{{Lang::get('home.I.have.read.and.accepted.the.Terms.and.conditions.and.Privacy.policy')}}</label>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</section>

@endsection