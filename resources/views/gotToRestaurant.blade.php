<?php  
use App\City, App\Product, App\Order;
?>
@extends('innerpageLayout')
@section('title', Lang::get('common.title'))
@section('content')

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

                <div class="col-md-8"> 
                    <ul class="restro-tab-btn" role="tablist" id="menuTabs">
                        <li class="active menu_review_info_li" role="presentation">
                            <a href="#menuTab" aria-controls="menuTab" class='menu_review_info' role="tab" data-toggle="tab"><i class="flaticon-interface-2"></i> {{Lang::get('home.Menu')}}</a>
                        </li>
                        <li class="menu_review_info_li" role="presentation">
                            <a href="#reviewTab" aria-controls="reviewTab" class='menu_review_info' role="tab" data-toggle="tab"><i class="flaticon-hand"></i>{{Lang::get('home.Review')}}</a>
                        </li>
                        <li class="menu_review_info_li" class="clearfix" role="presentation">
                            <a href="#infoTab" aria-controls="infoTab" class='menu_review_info' role="tab" data-toggle="tab"><i class="flaticon-shapes"></i>{{Lang::get('home.Info')}}</a>
                        </li> 
                    </ul>
                    
                    <div class="restro-menu-tab tab-content">

                        <div role="tabpanel" class="tab-pane fade in active" id="menuTab"> 
                            <div class="menu-item-left">
                               <div class="menu-item-list">
                                  <h4>{{Lang::get('home.Delivery.Menu')}}</h4>
                                  <ul>
                                     {{-- */$productTypeInc=1;/* --}}
                                     @foreach($restaurantMenu as $restaurantMenuname) 
                                        {{-- */ $active='';                                    
                                            if($productTypeInc==1)
                                               $active = 'active';                                            
                                        /* --}}                                       
                                        <li class="{{ $active }}">
                                            <a href="#{{str_replace(" ","", $restaurantMenuname->id)}}">{{ucfirst($restaurantMenuname->name)}}</a>
                                        </li>
                                        {{-- */ $productTypeInc++; /* --}}                      
                                     @endforeach                                        
                                  </ul>
                               </div>
								            </div>
                            
                            {{-- */ $prod_type =''; /* --}}
                            
								            <div class="menu-item-data @if($flag_close) restroclose @endif">

          								    @foreach($restaurantproduct as $product)                                                           
                                  @if($prod_type != $product['menu_id'])
          										        {{-- */ $prod_type = $product['menu_id']; /* --}}
                  										<?php 
                                          if(!empty($product->menu->image->location)) {
                  													$image = $product->menu->image->location;
                                          } else {
                                            $image = $restaurantImage;
                                          }
                  										?>
                									    <div id="{{isset($product->menu->id) ? str_replace(' ', '', $product->menu->id) : ''}}" class="item-data-heading" style="background-image:url({{$image}});">
                  										    <span>{{ isset($product->menu->name) ? ucfirst($product->menu->name) : '' }}</span>
              									      </div>
                                  @endif
                                  
                                  {{-- */ 
                                  // Product::productOptions($product['id']);      
                                      $productitemscount = Product::productOptions($product['id']);                                  
                                  /* --}}
                                  
                                  <div class="row">
                                      <div class="col-md-7 item-name" rel="{{$product['id']}}">
                                        <div class="media">
                                          <div class="media-left">
                                              <img src="{{ isset($product->image->location) ? $product->image->location :'/images/image.jpg' }}" alt="" title="" class="img-circle">
                                          </div>
                                          <div class="media-right">
                                              <h2>{{$product['name']}}</h2>
                                              <h3>{{format($product['cost'])}}</h3>
                                          </div>
                                        </div>
                                      </div>
                                      
                                      <div class="col-md-3">                                          
                                        @if($flag_close)                                        
                                          <span class="qty-subb"><i class="fa fa-minus-circle"></i></span>
                                          <span class="qty-inputt"><input disabled="disabled"  type="text" value="0"></span>
                                          <span class="qty-addd"><i class="fa fa-plus-circle"></i></span>
                                        @else
                                          <span field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['id']}}" class="@if($productitemscount>0){{'hasaddons'}}@else{{'noaddons'}}@endif qtyminus qty-sub" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['id']}}" @endif ><i class="fa fa-minus-circle"></i></span>
                                          
                                          <span class="qty-input"><input name="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['id']}}" type="text" value="@if(isset($cart->getData()[$product['id']]['quantity'])){{$cart->getData()[$product['id']]['quantity'] }} @else{{0}}@endif"></span>

                                          <span field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['id']}}" class="qtyplus qty-add @if($productitemscount>0){{'hasaddons'}}@else{{'noaddons'}}@endif" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['id']}}" @endif ><i class="fa fa-plus-circle"></i></span>
                                        @endif  
                                        
                                        <input name="pageurl" type="hidden" value="{{$restaurantUrl}}">
                                        
                                        <!-- Bhuvnesh ProductAddons popup start -->
                                        <div class="modal fade" id="ProdAddon{{$product['id']}}" role="dialog">
                                            <div class="modal-dialog">    
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">  {{$product['name']}}</h4>
                                                    </div>

                                                    <div class="modal-body">                                                       
                                                      {{-- */ 
                                                        $productaddons = Product::productAddons($product['id']);                                                          
                                                      /* --}}

                                                      <div class="content-block choices-toppings__container">                                                                  
                                                        {!! Form::open(array('class' =>'orderitemaddons')) !!} 
                                                          <input type="hidden" name="productid[]" value="{{$product['id']}}">

                                                          <input type="hidden" name="itemname" value="{{$product['name']}}">

                                                          <input type="hidden" name="itemprice" value="{{$product['cost']}}"> 

                                                          <div class="choices-toppings__elements__wrapper choices-toppings__product__information toggle-elements-container choices-toppings__product__information--no-image">
                                                              <table class="table-cart">
                                                                  <tbody>
                                                                      <tr>
                                                                        <td class="choices-toppings__product__information__text">
                                                                            <label for="cart_product_skeleton_quantity" class="control-label required">
                                                                            Quantity</label>
                                                                        </td>
                                                                        <td class="choices-toppings__product__information__quantity">                               
                                                                          <div class="choices-toppings__product__information__text__title">
                                                                               
                                                                          </div>
                                                                          <div class="choices-toppings__product__information__text__description">
                                                                          </div>

                                                                          <input type="number" min="0" value="<?=isset($cart->getData()[$product['id']]['quantity'])?$cart->getData()[$product['id']]['quantity']:1 ?>"  class="form-control number" name="cart_product_quantity" >
                                                                        </td>
                                                                      </tr>
                                                                   </tbody>
                                                              </table>
                                                          </div>

                                                          @foreach($productaddons as $key => $productaddon)                        
                                                            <div class="choices-toppings__wrapper toggle-elements-container">    
                                                                <div class="choices-toppings__elements__wrapper choices-toppings-checkbox-wrapper">
                                                                  <div class="choices-toppings__title__container">
                                                                     <h4 class="choices-toppings__title">
                                                                        <?php 
                                                                          echo ($productaddon[0]['required']=="Y")?$key."*":$key;
																		                                      $key = preg_replace('/\s+/', '', $key); 
                                                                        ?>
                                                                        <span hidden=true id="{{$key}}{{$productaddon[0]['required']}}" style='color:red;font-size: 14px;'>
                                                                            {{Lang::get('validation.option_selection_reqired')}}
                                                                         </span>
                                                                      </h4> 
                                                                  </div>
                                                                  <div class="row">     
                                                                    @foreach($productaddon as $itemdetail)  
                                                                      <div class="col-sm-6">   
                                                                        <div class="{{$itemdetail['type']}}">
                                                                            <label>
                                                                              <input 
                                                                                <?php 
                                                                                if($itemdetail['type']=='checkbox'){ echo "name=itemaddond[]";}else{ echo "name='itemaddond[]".$key."'"; } 
                                                                                if(isset($cart->getData()[$product['id']]['itemaddond']) && in_array($itemdetail['option_item_id'],$cart->getData()[$product['id']]['itemaddond']))
                                                                                  echo ' checked=true';
                                                                                ?>
                                                                                type="{{$itemdetail['type']}}" value="{{$itemdetail['option_item_id']}}"
                                                                                class="{{$productaddon[0]['required']}} {{$key}}{{$productaddon[0]['required']}}" rel="{{$key}}"
                                                                              >
                                                                              {{$itemdetail['item_name']}} ({{format($itemdetail['price'])}})
                                                                            </label>
                                                                        </div>          
                                                                     </div>
                                                                    @endforeach                                                 
                                                                  </div>                
                                                                </div>
                                                            </div> 
                                                          @endforeach

                                                          <div class="choices-toppings__submit">
                                                              <button class="btn btn-primary btn-lg choices-toppings__button--submit" name="cart_product_skeleton[submit]" id="cart_product_skeleton_submit" type="button">Submit</button>
                                                          </div>   

                                                        {!! Form::close() !!}

                                                      </div>
                                                    </div> 

                                                </div>

                                            </div>
                                        </div>
                                        <!-- Bhuvnesh ProductAddons popup end -->

                                      </div>
                                  </div>

                              @endforeach                              
                            </div>

                            <div class="clearfix"></div>

                        </div>


              					<div role="tabpanel" class="tab-pane fade" id="reviewTab">
                            <!-- ========review post========-->
                            @if(Auth::check())
                              <?php 
                                $order = Order::select('id')->where(['user_id'=>Auth::user()->id,'restaurant_id'=>$restaurantdetails->id])->first();
                              ?>

                              @if(!empty($order))

                                {!! Form::open(['class'=>'filter-form','id'=>'review_form','url'=>'review/post']) !!}

                                  @if(Session::has('flash_message'))
                                      <div class="alert alert-success">
                                          {{ Session::get('flash_message') }}    
                                      </div>
                                  @endif

                                  @if($errors->any())
                                      <div class="alert alert-danger">
                                          @foreach($errors->all() as $error)
                                              <p>{{ $error }}</p>
                                          @endforeach
                                      </div>
                                  @endif

                                  {!! Form::hidden('restaurant_id',$restaurantdetails->id) !!}

                                  <div class="row">
                                    <div class="rating-cont clearfix">
                                      <label for="form_message text-left">{{trans('home.rating')}}*</label>
                                      <fieldset class="rating">                
                                          <input type="radio" value="5" name="rating" id="star5"><label title="Rocks!" for="star5"></label>
                                          <input type="radio" value="4" name="rating" id="star4" checked="checked"><label title="Pretty good" for="star4"></label>
                                          <input type="radio" value="3" name="rating" id="star3"><label title="Meh" for="star3"></label>
                                          <input type="radio" value="2" name="rating" id="star2"><label title="Kinda bad" for="star2"></label>
                                          <input type="radio" value="1" name="rating" id="star1"><label title="Sucks big time" for="star1">
                                      </fieldset>
                                    </div>                    
                                  </div>

                                  <div class="row">
                                      <div class="col-md-12">
                                          <div class="form-group">
                                            <label for="form_message">{{trans('home.review')}}*</label>
                                            {!! Form::textarea('review', null, ['class' => 'form-control','rows'=>4,'required'=>'required','data-error'=>'Please,leave us a review']) !!}  
                                          </div>
                                      </div>  
                                  </div>

                                  <div class="row">
                                    <div class="col-md-12">
                                      <input type="submit" class="btn btn-success btn-send text-right" value="{{trans('home.post')}}">
                                    </div>
                                  </div>
                                  <br><br>

                                {!! Form::close() !!}

                              @endif

                            @endif
                            <!-- ==end of review post====== -->

                						<div class="reviews-list">
                							@if(count($rewiewdetails) <= 0 )
                								<h4>No Review ...</h4>
                							@else
                								@foreach($rewiewdetails as $review)
              								    <div class="single-review">
                  										<div class="review-user-thumb" style="background: url('{{url('images/cart-empty.png')}}');"></div>
                  										<h4>{{$review->first_name}}</h4>
                  										<div class="re-date">
                  											{{date("jS F, Y", strtotime($review->date))}}
                  										</div>
                  										<div class="user-rating">
                  											<div class="u-rating-outer">
                  												<span class="rating-static rating-{{$review->rating*10}}"></span>
                  											</div>
                  										</div>
                  										<p>{{$review->review}}</p>
                									</div><!--/single review-->
                								@endforeach 
                							@endif 
                						</div>

              					</div>

      				          <div role="tabpanel" class="tab-pane fade" id="infoTab">
                          <div class="restro-info-tab">
                              <div class="icon-heading"><i class="flaticon-clock"></i>Restaurant Hours</div>
                              <table class="table">
                                  @foreach($resauranttimes as $ResDetails)
                                      <tr>
                                          <td>
                                           {{Lang::get('common.weekday_'.$ResDetails->weekday)}}
                                          </td>
                                          <td class="text-right">{{substr($ResDetails->open,0,-3)}} - {{substr($ResDetails->closing,0,-3)}}</td>
                                      </tr>
                                   @endforeach
                              </table>
                              <div class="icon-heading"><i class="flaticon-clock"></i>Delivery Hours</div>
                              <table class="table">
                                  @foreach($resauranttimes as $ResDetails)
                                      <tr>
                                          <td>
                                           {{Lang::get('common.weekday_'.$ResDetails->weekday)}}
                                          </td>
                                          <td class="text-right">{{substr($ResDetails->delivery_start,0,-3)}} - {{substr($ResDetails->delivery_end,0,-3)}}</td>
                                      </tr>
                                   @endforeach
                              </table>

                              <div class="row">
                                  <div class="col-md-6">
                                    <div class="r-info-widgets">
                                        <div class="icon-heading"><i class="flaticon-location"></i>Address</div>
                                        <div class="r-info-data">
            							                  @if(!empty($restaurantdetails->company))
                                                <p>Company :{{$restaurantdetails->company}}</p>
                                            @endif
                                            @if(!empty($restaurantdetails->floor))
                                                <p>Floor :{{$restaurantdetails->floor}}</p>
                                            @endif
                                            @if(!empty($restaurantdetails->name))
                                                <p>Name :{{$restaurantdetails->name}}</p>
                                            @endif
                                            @if(!empty($restaurantdetails->area))
                                                <p>Area :{{$restaurantdetails->area}}</p>
                                            @endif
                                            @if(!empty($restaurantdetails->city))
                                                <p>City: {{$restaurantdetails->city}}</p>
            							                  @endif
                                        </div>
                                    </div>
                                  </div>

                                  <div class="col-md-6">
                                      <div class="r-info-widgets">
                                          <div class="icon-heading"><i class="flaticon-money"></i>Payment Types</div>
                                          <div class="r-info-data">
                                              <div class="payment-options">
                                                  <img src="{{url('images/cash-option-img.png')}}" alt="">
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                          </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-4">

                    @include('cart.cart')

                    @if(Auth::user())
                        <a href="{{url('/proceedtocheckout/'.$restaurantUrl)}}"><div class="checkout-btn">{{ Lang::get('proceedtocheckout.Proceed.to.Checkout') }}</div> </a>
                    @else
                      <a href="{{url('/auth/login')}}" data-target="#LoginPop" data-toggle="modal"><div class="checkout-btn">{{ Lang::get('proceedtocheckout.Proceed.to.Checkout') }}</div></a>
                    @endif

                    <div class="restro-menu-details" >
                        <ul>
                          <li> 
                            <div class="row">
              								<div class="col-md-1">	
              									<i class="flaticon-timer"></i>
              								</div>
              								<div class="col-md-6">
              									{{Lang::get('home.Delivery.Time')}}
              								</div>
              								<div class="col-md-5">									
                                {{(isset($restaurantdetails->delivery_time) && !empty($restaurantdetails->delivery_time))?$restaurantdetails->delivery_time:Config::get('constants.delivery_time')}}min                      
              								</div>
    							         </div>
    						          </li>

    						          <li>
                            <div class="row" >
              								<div class="col-md-1">	
              									<i class="flaticon-coins"></i>
              								</div>
              								<div class="col-md-6">
              									{{Lang::get('home.Delivery.Fee')}}
              								</div>
              								<div class="col-md-5">
              									<?php
              									if(!empty($cart->deliveryCharges())) {
                                  $delivery_charge =  $cart->deliveryCharges();
                                  if(is_numeric($delivery_charge)) {
                                    echo $delivery_charge.Config::get('constants.currency');
                                  } else {
                                    echo $delivery_charge;
                                  }                                          
                                }	else {
                                  $delivery_charge =  (isset($restaurantdetails->delivery_charge) && !empty($restaurantdetails->delivery_charge))?$restaurantdetails->delivery_charge:Config::get('constants.delivery_charge');                    
                                  if(is_numeric($delivery_charge)){
                                    echo $delivery_charge.Config::get('constants.currency');
                                  }else{
                                    echo $delivery_charge;
                                  }
                                }										
              									?>
              								</div>
    							          </div>
    						          </li>

                        {{--    <li> <div class="row" >
                                <div class="col-md-1">  
                                  <i class="flaticon-ribbon"></i>
                                </div>
                                <div class="col-md-6">
                                  {{Lang::get('home.packaging_fees')}}
                                </div>
                                <div class="col-md-5">
                                  {{ $restaurantdetails->packaging_fees.' '.Config::get('constants.currency') }}
                                </div>
                              </div>
                            </li> --}}

              						<li> <div class="row" >
              								<div class="col-md-1">	
              									<i class="flaticon-up-arrow"></i>
              								</div>
              								<div class="col-md-6">
              									{{Lang::get('home.No.Minimum.Order')}}
              								</div>
              								<div class="col-md-5">
              									{{(isset($restaurantdetails->min_order) && !empty($restaurantdetails->min_order))?$restaurantdetails->min_order:Config::get('constants.min_order')}}{{Config::get('constants.currency')}}
              								</div>
              							</div>
              						</li>

              						<li> <div class="row" >
              								<div class="col-md-1">	
              									<i class="flaticon-ribbon"></i>
              								</div>
              								<div class="col-md-6">
              									{{Lang::get('home.Accept.Vouchers')}}
              								</div>
              								<div class="col-md-5">
              									@if(!is_null($restaurantdetails->coupon))
                                    YES
                                 @else
                                    No
                                 @endif
              								</div>
              							</div>
              						</li>

    			              </ul>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

@endsection