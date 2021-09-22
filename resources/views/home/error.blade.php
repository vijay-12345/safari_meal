<?php  
use App\City;
?>
@extends('innerpageLayout')
@section('title', Lang::get('common.title'))
@section('content')
<?php
	$error = Request::get('message');
			
?>
<div class="inner-page-header">
	<div class="container">
		<div class="breadcrumbs-cont">
			<p><a href="{{url('/')}}" title="">Home</a> / <a href="" title="">Restaurants</a></p>
		</div>
	</div>
</div>
<div class="inner-page-data">
	<div class="container">
		<div class="sidebar-data">
			<div class="row">
				<div class="col-md-4">
					<div class="sidebar">
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
						<div class="sidebar-widget no-margin no-border filtercousine">
							<div class="sw-heading">
								<i class="flaticon-cross"></i>
								<h4>{{Lang::get('home.Cuisines')}}</h4>
							</div>
						</div><!--/sidebar widget-->
						{!! Form::close() !!}
					</div>
				</div>
				<div class="col-md-8">
					<div class="restro-list">						
						<h3>
							{{$ErrorMessage}}
						</h3>
						</div>				
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
