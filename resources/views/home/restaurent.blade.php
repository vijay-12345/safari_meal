<?php  
use App\City;
?>
@extends('innerpageLayout')

@section('title', Lang::get('common.title'))

@section('content')

<?php
	$cities = City::lang()->where('status',1)->get();		
	$city = Request::get('searchcity');
	$area = Request::get('searcharea');
	$addQustionmark = '';
	if (strpos(Request::url(),'?') !== false) {		
		$addQustionmark = '';
	} else {
		$addQustionmark = "?";
	}		
?>

<div class="inner-page-header">
	<div class="container">
		<!--div class="breadcrumbs-cont">
			<p><a href="{{url('')}}" title="">Home</a> / <a href="{{url('/'.$restaurentUrl)}}" title="">Restaurants</a></p>
		</div>
		<h3>{{Lang::get('home.Order.from')}} {{$restrocount}} restaurants in {{$areaLatLong->city->name}}</h3-->
		<div class="row">
			<div class="col-sm-6">
				<div class="breadcrumbs-cont">
					<p><a href="{{url('')}}" title="">Home</a> / <a href="{{url('/'.$restaurentUrl)}}" title="">Restaurants</a></p>
				</div>
				@if($restrocount > 0)
				<h3>{{Lang::get('home.Order.from')}} {{$restrocount}} restaurants in {{$areaLatLong->city->name}}</h3>
				@else
				<h3>{{Lang::get('home.there.are.no.restaurants.meeting.your.search.criterion')}}</h3>
				@endif
			</div>
			<div class="col-sm-6">
				<div class="change-location-btn text-right">
					<button type="button" class="loc-btn"><i class="flaticon-gps"></i>Change Location<span class="flaticon-arrows"></span></button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="filter-search-full">	
	<div class="container search-from">
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
						<i class="flaticon-interface"></i>
						<button disabled="disabled" type="button" class="btn btn-srch">{{Lang::get('home.Show Restaurants')}}</button>						
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</div>
</div>

<div class="inner-page-data">
	<div class="container">
		<div class="sidebar-data">
			<div class="row">
				<div class="col-md-4">
					<div class="sidebar">
						{!! Form::open(array('class' => 'filterform','url' => 'restaurentlist/'.$restaurantUrl,'method'=>'get')) !!}
							
							<div class="search-reset-filter">
								<div class="row">
									<div class="col-xs-6"><span class="filter-text">Filters</span></div>
									<div class="col-xs-6 text-right"><button type="button" id="reset" class="btn btn-link">Clear All</button></div>
								</div>
								
								<!-- <button type="button" id="reset" class="btn btn-primary">Reset Search</button> -->
							</div>

							<div class="search-area">
								<input value ="<?php if(Request::get('restauranttitle')){ echo Request::get('restauranttitle');} ?>" name="restauranttitle" type="text" class="form-control icon-left" placeholder="{{Lang::get('home.Search')}}">
								<i class="fa fa-search"></i>
							</div>
							<div class="sidebar-widget">
								<div class="sw-heading">
									<i class="flaticon-interface-1"></i>
									<h4>{{Lang::get('home.Sort.By')}}: {{Lang::get('home.Ratings')}}</h4>
								</div>
								<div class="rating-cont clearfix">
									<fieldset class="rating">							   
									    <input <?php if(Request::get('rating')==5){ echo "checked='checked'";} ?> type="radio" id="star5" name="rating" value="5"><label for="star5" title="Rocks!"></label>
									    <input <?php if(Request::get('rating')==4){ echo "checked='checked'";} ?> type="radio" id="star4" name="rating" value="4"><label for="star4" title="Pretty good"></label>
									    <input <?php if(Request::get('rating')==3){ echo "checked='checked'";} ?> type="radio" id="star3" name="rating" value="3"><label for="star3" title="Meh"></label>
									    <input <?php if(Request::get('rating')==2){ echo "checked='checked'";} ?> type="radio" id="star2" name="rating" value="2"><label for="star2" title="Kinda bad"></label>
									    <input <?php if(Request::get('rating')==1){ echo "checked='checked'";} ?> type="radio" id="star1" name="rating" value="1"><label for="star1" title="Sucks big time"></label-->
									</fieldset>
								</div>
							</div><!--/sidebar widget-->
							<div class="sidebar-widget">
								<div class="sw-heading">
									<!-- <i class="fa fa-cutlery"></i> -->
									<i class="flaticon-cross"></i>
									<h4>{{Lang::get('home.Type')}}</h4>
								</div>
								<div class="checkbox-cont">
									<input type="checkbox" name="is_restaurant" {{ Request::get('is_restaurant')==1 ? 'checked' : ''}} value='1' class="css-checkbox" id='is_restaurant'>
									<label class="css-label " for='is_restaurant'>{{ Lang::get('home.Restaurants')}} </label>
								</div>
								<div class="checkbox-cont">
									<input type="checkbox" name="home_cooked" {{ Request::get('home_cooked')==1 ? 'checked' : ''}} value='1' class="css-checkbox" id='home_cooked'>
									<label class="css-label " for='home_cooked'>{{ Lang::get('home.home.cooked')}}</label>
								</div>
							</div>
							<div class="sidebar-widget">
								<div class="sw-heading">
									<i class="flaticon-tool"></i>
									<h4>{{Lang::get('home.Filter.Restaurants')}}</h4>
								</div>						
								<div class="checkbox-cont">
									<input type="checkbox" name="filterRestaurants[deals]" id="checkboxG1" class="css-checkbox" <?php if(isset(Request::get('filterRestaurants')['deals']) && !empty(Request::get('filterRestaurants')['deals'])){ echo "checked='checked'"; } ?> >
									<label for="checkboxG1" class="css-label">{{Lang::get('home.Deals')}}</label>
								</div>
								<div class="checkbox-cont">
									<input type="checkbox" name="filterRestaurants[open]" id="checkboxG2" class="css-checkbox" <?php if(isset(Request::get('filterRestaurants')['open'])){ echo "checked='checked'"; } ?> >
									<label for="checkboxG2" class="css-label">{{Lang::get('home.Open.Restaurants')}}</label>
								</div>
							</div><!--/sidebar widget-->
							<div class="sidebar-widget">
								<div class="sw-heading">
									<!-- <i class="fa fa-cutlery"></i> -->
									<i class="flaticon-cross"></i>
									<h4>{{Lang::get('home.Type')}}</h4>
								</div>
								<div class="checkbox-cont">
									<input type="checkbox" name="veg" {{ Request::get('veg')==1 ? 'checked' : ''}} value='1' class="css-checkbox" id='veg'>
									<label class="css-label" for='veg'>Veg</label>
								</div>
								<div class="checkbox-cont">
									<input type="checkbox" name="non_veg" {{ Request::get('non_veg')==1 ? 'checked' : ''}} value='1' class="css-checkbox" id='non_veg'>
									<label class="css-label" for='non_veg'>Non Veg</label>
								</div>
							</div>	
							<div class="sidebar-widget no-margin no-border filtercousine">
								<div class="sw-heading">
									<i class="flaticon-cross"></i>
									<h4>{{Lang::get('home.Cuisines')}}</h4>
								</div>
								@foreach($cousine as $cuisine)							
								<div class="checkbox-cont">								
									<input <?php if(Request::get('cuisines') && (in_array($cuisine->name,Request::get('cuisines')) || in_array(ucfirst($cuisine->name),Request::get('cuisines')))){ echo "checked='checked'"; } ?> type="checkbox" name="cuisines[]" value='{{ucfirst($cuisine->name)}}' id="checkboxcG{{$cuisine->id}}" class="css-checkbox" >
									<label for="checkboxcG{{$cuisine->id}}" class="css-label">{{ucfirst($cuisine->name)}}</label>
								</div>
								@endforeach							
								@if($totalCuisines >= $cousinlimit)													
									<div class="btn-see-more-outer">
										<button rel ="{{$cousinlimit}}" type="button" class="btn btn-see-more">{{Lang::get('home.See.More')}}</button>
									</div>
								@endif							
								<input type="hidden" name="cousinlimit" class='cousinlimit' value='{{$cousinlimit}}'>
							</div><!--/sidebar widget-->
						{!! Form::close() !!}
					</div>
				</div>

				<div class="col-md-8">
					<div class="restro-list">	
						<?php
						$data = array();
						foreach($restaurent as $restaurentdetail) {
							if(in_array($restaurentdetail->id, $data)) continue;

							$data[] = $restaurentdetail->id;
							$timingstatus = "";
						 	$currenttime  = strtotime(__dateToTimezone('', date('H:i:s'), 'H:i:s'));
							$opentime     = strtotime($restaurentdetail['open']);						
							$closetime    = strtotime($restaurentdetail['closing']);
							if($currenttime < $opentime) {
								$timediff = ($opentime - $currenttime);							
								// $fsd = explode(":", date("H:i", $timediff));
								$hours = floor($timediff / 3600);
								$minutes = floor(($timediff / 60) % 60);
								$timingstatus .=Lang::get('home.Opens.in');
								if($hours != 0) $timingstatus .=' '.$hours.'h';
								if($minutes !=0) $timingstatus .=' '.$minutes.'min';
							} else if($restaurentdetail['open'] == 0) {
								$timingstatus =  'Today Closed.';
							} else if($currenttime > $opentime && $currenttime < $closetime) {
								$timingstatus =  'Open.';
							} else {
								$timingstatus =  'Closed.';
							}				
						?>						
							<tr>																
								<div class="single-restro">
									<div class="restro-list-data">	
										<?php 
										$image= !is_null($restaurentdetail->image) ? $restaurentdetail->image->logo_location : '/images/default-restaurantlog.png'; ?>
										<div class="restro-thumb" style="background: url('{{url($image)}}');"></div>
										<div class="title">									
											<a href="#" title="{{$restaurentdetail['name']}}">{{$restaurentdetail['name']}}</a>
											@if($restaurentdetail->is_home_cooked =='1'){{ Lang::get('home.home.cooked')}} @else {{ Lang::get('home.Restaurants')}} @endif
										</div>
										<div class="restro-list-bottom">
											<ul class="list-inline">
												<li>
													<div class="restro-time">
														@if($timingstatus == 'Open.')
															<i class="flaticon-time green-watch"></i>
														@else
															<i class="flaticon-time red-watch"></i>
														@endif
														{{$timingstatus}}
													</div>
												</li>
												<li class="clearfix">
													<div class="rating-star-cont">
														<span class="rating-static rating-{{$restaurentdetail['rating']*10}}"></span>
													</div>
													<span class="rating-count">{{$restaurentdetail['rating']}}</span>
													<span class="rating-text">{{Lang::get('home.Rating')}}   </span>
												</li>
											</ul>
										</div>

										<?php
											$location = $areaLatLong->latitude . ',' . $areaLatLong->longitude;
										?>
										<a href="{{url('/restaurentdetail/'.$restaurentdetail['restaurent_urlalias'] . '? =' . $location)}}" title="" class="restro-menu-btn btn btn-primary">{{Lang::get('home.Go.to.Menu')}}</a>
										@if($restaurentdetail->is_home_cooked =='0')
											<a href="{{url('/tablebooking/'.$restaurentdetail['restaurent_urlalias'] . '? =' . $location)}}" title="" class="btn btn-primary" style="position: inherit;left: 454px;top: -12px;width: 170px;height: 55px;line-height: 55px;padding: 0 15px;font-size: 22px;">Book Table</a>
										 @endif
									</div>
								</div><!--/single restro-->					
						<?php }
						echo $restaurent->appends(['searchcity'=>Request::input("searchcity"),'searcharea'=>Request::input("searcharea"),'sort' => 'id'])->render(); 
						?>

						@if($restrocount == 0)
							<h3 style="text-align: center;">{{Lang::get('home.there.are.no.restaurants.meeting.your.search.criterion')}}</h3>
						@endif
												
					</div>				
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
