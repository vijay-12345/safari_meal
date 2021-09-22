<?php  
use App\City, App\Product, App\Order;
?>
@extends('innerpageLayout')
@section('title', Lang::get('common.title'))
@section('content')


<script type="text/javascript" src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>

<?php 
  $cart = new App\Cart;
?>
@if(Session::has('Successmessage'))
	<div class="alert alert-success">
	   {{Session::pull('Successmessage')}}
	</div>
@endif
@if(Session::has('Errormessage')) 
	<div class="alert alert-danger">
	   {{Session::pull('Errormessage')}}
	</div>
@endif

<div class="inner-page-data">
    <div class="container">
		    <div class="menu-restro">
          
            <div class="restro-list-data">
      				  <?php
      				      $restaurantImage	= !is_null($restaurantdetails->image)?$restaurantdetails->image->logo_location:'images/default-restaurantlog.png';
      				  ?>
                <!-- {{$restaurantdetails->id}} -->
                <div class="restro-thumb" style="background: #fff url('{{url($restaurantImage)}}');"></div>
                <div class="title">
                     <!--<a href="#" title="{{$restaurantdetails->company}}">{{$restaurantdetails->company}}</a>-->
                </div>                                          
                <p>{{$restaurantdetails->name}}</p> 
                
                <div class="restro-list-bottom">
                    <ul class="list-inline">
                        <?php 
                            $timingstatus = "";
                            $flag_close   = false;
                            $currenttime  = strtotime(__dateToTimezone('', date('H:i:s'), 'H:i:s'));
                            $opentime     = strtotime($restaurantdetails->open);
                            $closetime    = strtotime($restaurantdetails->closing);
                            if($currenttime < $opentime) {
                                $timediff = ($opentime - $currenttime);
                                // $fsd = explode(":",date("H:i", $timediff));
                                $hours = floor($timediff / 3600);
                                $minutes = floor(($timediff / 60) % 60);
                                $timingstatus .= Lang::get('home.Opens.in');
                                if($hours != 0) $timingstatus .=' '.$hours.'h';
                                if($minutes !=0) $timingstatus .=' '.$minutes.'min';
                                $flag_close = true;                 
                            } else if($restaurantdetails->open==0) {
                                $flag_close = true;
                                $timingstatus =  Lang::get('home.Today').' '.Lang::get('home.Closed').'.';
                            } else if($currenttime>$opentime && $currenttime < $closetime) {
                                $timingstatus =  Lang::get('home.Open.');
                            } else {
                                $timingstatus =  Lang::get('home.Closed').'.';
                                $flag_close = true;
                            }                            
                        ?>
                        
                        <li><div class="restro-time">
                              @if($timingstatus == 'Open.')
                                <i class="flaticon-time green-watch"></i>
                              @else
                                <i class="flaticon-time red-watch"></i>
                              @endif
                              {{-- */ echo $timingstatus ;/* --}}
                          </div>
                        </li>
                        
                        <li class="clearfix">
                            <div class="rating-star-cont">
                                <span class="rating-static rating-{{$restaurantdetails->rating*10}}"></span>
                            </div>
                            <span class="rating-count">{{$restaurantdetails->rating}}</span>
                            <span class="rating-text">{{Lang::get('home.Rating')}}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
        </div><!--/single restro-->


         <div class="sidebar-data">
            <div class="row">
              <div class="col-md-1">
              </div>
              <div class="col-md-10">
              {!! Form::open(['class'=>'filter-form admin-table-add','url' => '/table/add']) !!}
              <input type="hidden" name="restaurant_id" value="{{$restaurantdetails->id}}" >
              @if(Auth::user())
                <input type="hidden" name="customer_id" value="{{Auth::id() }}" >
              @endif

              @if(Session::has('flash_message'))
                  <div class="alert alert-success">
                      {{ Session::pull('flash_message') }}     
                  </div>
              @endif

              @if($errors->any())
                  <div class="alert alert-danger">
                      @foreach($errors->all() as $error)
                          <p>{{ $error }}</p>
                      @endforeach
                  </div>
              @endif

              <div class="page-data-right">
                <div class="page-title">
                  <div class="row">
                    <div class="col-md-12">
                      <h3>{{trans('home.book.table')}}</h3>
                    </div>
                  </div>
                </div>
                  
                <div class="row">
                  <div class="col-md-10">
                    <div class="panel panel-default">
                      <div class="panel-body">                  
                        <div class="table-responsive">

                          <table class="table table-bordered no-margin">
                          <tr>
                              <td><h5>{{trans('home.customer.name')}}:</h5></td>
                              <td>
                                <input type="text" name="customer_name" id="customer_name" value="" placeholder="{{trans('home.customer.name')}}" class="form-control" autocomplete="off">
                              </td>
                            </tr>  
                            <tr>
                              <td><h5>{{trans('home.customer.contact')}}:</h5></td>
                              <td>
                                <input type="text" name="customer_contact" id="customer_contact" value="" placeholder="{{trans('home.customer.contact')}}" class="form-control" autocomplete="off">
                              </td>
                            </tr>
                            <tr>
                              <td><h5>{{trans('home.total.person')}}:</h5></td>
                              <td>
                                <input type="number" name="total_person" id="total_person" value="" placeholder="{{trans('home.total.person')}}" class="form-control" autocomplete="off">
                              </td>
                            </tr>
                            <tr> 
                              <td><h5>{{trans('home.book.date')}}:</h5></td>
                              <td>  
                                <input type="text" name="book_date" id="book_date" value="" placeholder="{{trans('home.book.date')}}" class="form-control" autocomplete="off">
                              </td>
                            </tr> 
                            <tr>
                              <td><h5>{{trans('home.book.time')}}:</h5></td>
                              <td>  
                               <!-- <input type="time" name="book_time" id="book_time" value="" placeholder="{{trans('home.book.date')}}" class="form-control" autocomplete="off">-->
                                <select name="book_time" class="form-control">
                                    <?php 
                                        $startTimeInterval = 60*60;
                                        $open = strtotime($restaurantdetails->open);
                                        $close = strtotime($restaurantdetails->closing);
                                        while($open<$close){
                                            echo "<option value='".date('H:i',$open).' - '.date('H:i',$open+$startTimeInterval)."'>".date('H:i',$open)." - ".date('H:i',$open+$startTimeInterval)."</option>";
                                            $open = $open+$startTimeInterval;
                                        } 
                                    ?>
                                </select>
                                
                              </td>
                            </tr>  
                            <tr>
                              <td>
                                @if(Auth::user())
                                  <input type='button' name="table_save_button" id="admin_table_add" value="{{trans('home.book.table')}}" class="form-control btn btn-primary"/>
                                @else
                                  <a href="{{url('/auth/login')}}" data-target="#LoginPop" data-toggle="modal"><input type='button' name="table_save_button"  value="{{trans('home.book.table')}}" class="form-control btn btn-primary"/></a>
                                @endif
                                
                              </td>
                            </tr>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                </div>
                  </div>
                </div>
          
          <!-- <div class="row" id="order_restaurant_search_result">
            <!-- ajax restaurent search result -->
         <!-- </div> -->
          
        </div>
        {!! Form::close() !!}
    </div>
</div>

@endsection

