<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.min.js"></script>
<link href="{{ asset('css/bootstrap-multiselect.css') }}" rel="stylesheet">

<?php

use App\City, App\State, App\Restaurent, App\Country, App\Area, App\Cuisine;

$localRestaurants = Restaurent::where(['status'=>1,'lang_id'=>\Config::get('app.fallback_locale')])->lists('name','id');

$cuisines = Cuisine::lang()->lists('name','id');
$countries = Country::lists('country_name','country_id');
$states = $cities = $areas = [];
?>

@if(empty($cuisineIdArr))
  <?php
    $cuisineIdArr = [];
  ?>
@endif

@if(!empty($restaurant))
  <?php
      $country_id = $restaurant->country_id;
      $states = State::where('country_id',$country_id)->lists('state_name','state_id');  
  ?>
@endif

@if(!empty($restaurant))
  <?php
      $state_id = $restaurant->state_id;
      $cities = City::lang()->where('state_id',$restaurant->state_id)->lists('name','id');  
  ?>
@endif

@if(!empty($restaurant))
  <?php
      $city_id = $restaurant->city_id;
      $areas = Area::lang()->where('city_id',$restaurant->city_id)->whereNotIn('url_alias',[''])->lists('name','id');   
  ?>
@endif
<br />

@if(\Config::get('app.fallback_locale') != \Config::get('app.locale_prefix'))
  <div class="form-group">
      <label class="control-label col-sm-2" for="name">{{trans('Restaurant in english version')}}:</label>
      <div class="col-sm-10">
          {!! Form::select('parent_id', $localRestaurants,null, ['class' => 'form-control']) !!} 
      </div>                    
  </div>
@endif

<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.restaurant.name')}}:</label>
  <div class="col-sm-10">
  	{!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Name']) !!}
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="phone">{{trans('admin.restaurant.phone')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('phone', null, ['class' => 'form-control','maxlength' => 10,'placeholder'=>'Phone', 'id' => 'phone']) !!}
  </div>
</div>

<div class="form-group">
    <label class="control-label col-sm-2" for="type">{{trans('admin.food.type')}}:</label>
    <div class="col-sm-10">
        <label class="checkbox-inline" for='veg'><input type="checkbox" id='veg' value="1" name='is_veg' {{ isset($restaurant) && $restaurant->is_veg==1 ? 'checked' : ''}}>Veg</label>
        <label class="checkbox-inline" for='nonveg'><input type="checkbox" id='nonveg' value="1" name='is_nonveg' {{ isset($restaurant) && $restaurant->is_nonveg==1 ? 'checked' : ''}}>Non Veg</label>
    </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="name">{{trans('admin.cuisine')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('cuisines[]',$cuisines, $cuisineIdArr, ['class' => 'form-control','id'=>'cuisine','multiple'=>'multiple']) !!}    
  </div>                    
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="logo">{{trans('admin.logo')}}:</label>
  <div class="col-sm-10">
    {!! Form::file('logo',null,['class'=>'form-control']) !!}<br>
      @if(isset($restaurant->image->logo_location) and !is_null($restaurant->image->logo_location))
        <?php        
          $restaurantLogo = $restaurant->image->logo_location;                                   
        ?>
        {!! Form::hidden('old_logo',$restaurant->image->logo_location) !!}  
        {!! Form::hidden('image_id',$restaurant->image->id) !!}                                       
        <img src="{{asset($restaurantLogo)  }}" width="50">                                                             
      @endif    
  </div>                    
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="image">{{trans('admin.background.image')}}:</label>
  <div class="col-sm-10">
    {!! Form::file('image',null,['class'=>'form-control']) !!}<br>
      @if(isset($restaurant->image->location) and !is_null($restaurant->image->location))
        <?php        
        $restaurantImage = $restaurant->image->location;                                   
        ?>
        {!! Form::hidden('old_image',$restaurant->image->location) !!}                                               
        <img src="{{asset($restaurantImage)  }}" width="50">                                                             
      @endif        
  </div>                    
</div>

<div class="panel-default">
<div class="panel-heading text-center" style="border-color:#fff;">{{trans('admin.basic.information')}}</div>
</div>
<br>

<div class="form-group">
  <label class="control-label col-sm-2" for="rating">{{trans('Rating')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('rating', [0,1,2,3,4,5],null, ['class' => 'form-control']) !!}    
  </div>                    
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="min_order">{{trans('admin.minimum.order')}}($):</label>
  <div class="col-sm-10">
    @if(Session::get('access.role') =='admin')
  	{!! Form::text('min_order', null, ['class' => 'form-control','placeholder'=>trans('1')]) !!}					    
    @elseif(isset($restaurant->min_order))
      {{$restaurant->min_order}}
    @else
      Administrators Only
    @endif
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="delivery_charge">{{trans('admin.delivery.charge')}}:</label>
  <div class="col-sm-10">
    @if(Session::get('access.role') =='admin')
    {!! Form::text('delivery_charge', null, ['class' => 'form-control','placeholder'=>trans('admin.delivery.charge')]) !!}              
    @elseif(isset($restaurant->delivery_charge))
      {{$restaurant->delivery_charge}}
    @else
      Administrators Only
    @endif
  </div>						      
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="delivery_applicable">Delivery charge applicable if order less than:</label>
  <div class="col-sm-10">
    {!! Form::text('delivery_applicable', null, ['class' => 'form-control']) !!}
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="delivery_time">{{trans('admin.delivery.time')}}:</label>
  <div class="col-sm-10">
    @if(Session::get('access.role') =='admin')
    {!! Form::text('delivery_time', null, ['class' => 'form-control','placeholder'=>trans('admin.delivery.time')]) !!}              
    @elseif(isset($restaurant->delivery_time))
      {{$restaurant->delivery_time}}
    @else
      Administrators Only
    @endif    
  </div>							      
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="flat_delivery_time">Calculate delivery time based on distance:</label>
  <div class="col-sm-10">
      {!! Form::select('flat_delivery_time', ['2'=>'Yes', '1'=>'No'], null, ['class' => 'form-control']) !!}    
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="packaging_charge">Packaging Charge:</label>
  <div class="col-sm-10">
    @if(Session::get('access.role') =='admin')
      {!! Form::text('packaging_fees', null, ['class' => 'form-control numeric', 'placeholder' => 'Packaging Charge']) !!}
    @else
      Administrators Only
    @endif
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="cgst">CGST: (%)</label>
  <div class="col-sm-10">
    @if(Session::get('access.role') =='admin')
      {!! Form::text('cgst', null, ['class' => 'form-control', 'placeholder'=> 'Central GST']) !!}
    @else
      Administrators Only
    @endif
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="cgst">SGST: (%)</label>
  <div class="col-sm-10">
    @if(Session::get('access.role') =='admin')
      {!! Form::text('sgst', null, ['class' => 'form-control numeric', 'placeholder'=> 'State GST']) !!}
    @else
      Administrators Only
    @endif
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="cgst">Commission: (%)</label>
  <div class="col-sm-10">
    @if(Session::get('access.role') =='admin')
      {!! Form::text('admin_commission', null, ['class' => 'form-control numeric', 'placeholder'=> 'Commission']) !!}
    @else
      Administrators Only
    @endif
  </div>
</div>

<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.featured')}}:</label>
  <div class="col-sm-10">
    {!! Form::checkbox('featured',1,null, ['class' => '']) !!}    
  </div>                    
</div> 

<div class="form-group">
  <label class="control-label col-sm-2" for="order_status">Confirm Order:</label>
  <div class="col-sm-10">
      {!! Form::select('confirm_order', ['1'=>'Manual', '2'=>'Automatic'], null, ['class' => 'form-control']) !!}    
  </div>
</div>

<!-- <div class="form-group">
  <label class="control-label col-sm-2" for="order_status">Order Status:</label>
  <div class="col-sm-10">
      {!! Form::select('order_status', ['1'=>'Manual', '2'=>'Open For Drivers'], null, ['class' => 'form-control']) !!}    
  </div>
</div> -->

{!! Form::hidden('lang_id',\Config::get('app.locale_prefix'))!!}

<div class="form-group">
  <label class="control-label col-sm-2" for="status">{{trans('admin.status')}}:</label>
  <div class="col-sm-10">
    {!! Form::select('status', ['1'=>'Active','0'=>'Inactive'],null, ['class' => 'form-control']) !!}    
  </div>                    
</div>

<div class="panel-default">
<div class="panel-heading text-center" style="border-color:#fff;">{{trans('admin.address')}}:</div>
</div>
<br>
<div class="form-group">
  <label class="control-label col-sm-2" for="tax_per_order">{{trans('admin.floor')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('floor', null, ['class' => 'form-control','placeholder'=>trans('Floor')]) !!}              
    
  </div>                    
</div> 
<div class="form-group">
  <label class="control-label col-sm-2" for="street">{{trans('admin.address')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('street', null, ['class' => 'form-control','placeholder'=>trans('admin.address')]) !!}             
    
  </div>                    
</div>  
<div class="form-group">
    <label class="control-label col-sm-2" for="name">Country*:</label>
    <div class="col-sm-10">                   
  {!! Form::select('country_id', $countries, null, ['class'=>'country form-control']) !!}                   
</div>
</div>
<div class="form-group">
    <label class="control-label col-sm-2" for="name">{{trans('admin.state')}}:*:</label>
    <div class="col-sm-10">                 
  {!! Form::select('state_id', [null=>trans('Please Select')]+$states, null, ['class'=>'state form-control']) !!}                    
</div>
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="city_id">{{trans('admin.city')}}:*:</label>
  <div class="col-sm-10">
    {!! Form::select('city_id', [null=>trans('Please Select')]+$cities,null, ['class' => 'city form-control']) !!}    
    
  </div>                    
</div> 
<div class="form-group">
  <label class="control-label col-sm-2" for="area_id">{{trans('admin.area')}}*:</label>
  <div class="col-sm-10" id="area_box">
    {!! Form::select('area_id', [null=>trans('Please Select')] +$areas,null, ['class' => 'area form-control']) !!}    
    
    <br>    
    <a href="#" class="add-area">{{trans('admin.add.new.area')}}</a>  
      
 
  </div>                    
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="latitude">{{trans('admin.latitude')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('latitude', null, ['class' => 'form-control', 'placeholder'=>trans('admin.latitude')]) !!}    
  </div>
</div>
<div class="form-group">
  <label class="control-label col-sm-2" for="longitude">{{trans('admin.longitude')}}:</label>
  <div class="col-sm-10">
    {!! Form::text('longitude', null, ['class' => 'form-control', 'placeholder'=>trans('admin.longitude')]) !!}    
<p>In order for the restaurant to appear in searches these values are required. <a href="https://www.latlong.net/" target="_blank">Click here</a> to generate latitude and longitude online.</p>
  </div>
</div>
<script type="text/javascript">
  $(function() {    
      $('#cuisine').multiselect({
          includeSelectAllOption: true
      });

  });
</script>