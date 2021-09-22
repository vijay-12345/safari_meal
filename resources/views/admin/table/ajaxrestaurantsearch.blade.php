<?php  
  use App\City, App\Product;
  $cart = new App\Cart;
  $prefix = Session::get('access.role');
?>

@if($success)

    <script type="text/javascript" src="{{ asset('js/site.js') }}"></script>
    
    <?php
      $timingstatus = "";
      $flag_close = false;
      $currenttime = strtotime(__dateToTimezone('', date('H:i:s'), 'H:i:s'));
      $opentime    = strtotime($restaurantdetails->open);                       
      $closetime   = strtotime($restaurantdetails->closing);  
      if($currenttime < $opentime) {
      	$timediff = ($opentime - $currenttime);                         
      	// $fsd = explode(":",date("H:i", $timediff));
      	$timingstatus .=Lang::get('home.Opens.in');
        
        $hours = floor($timediff / 3600);
        $minutes = floor(($timediff / 60) % 60);
        $timingstatus .=Lang::get('home.Opens.in');
        if($hours != 0) {
          $timingstatus .=' '.$hours.'h';
        }
        if($minutes !=0) {
          $timingstatus .=' '.$minutes.'min';
        }
      	// if($fsd[0]!=0) {
      	// 	  $timingstatus .=' '.$fsd[0].'h';
      	// }
      	// if($fsd[1]!=0) {
      	// 	  $timingstatus .=' '.$fsd[1].'min';
      	// }
        $flag_close = true;               
      } else if($restaurantdetails->open==0) {
        $flag_close = true;
        $timingstatus =  Lang::get('home.Today').' '.Lang::get('home.Closed').'.';
      } else if($currenttime	>	$opentime && $currenttime < $closetime) {
    	   $timingstatus =  Lang::get('home.Already.Open.');
      } else {
        $timingstatus =  Lang::get('home.Closed').'.';
        $flag_close = true;
      }                           
    ?>

    <input type="hidden" name="check_delivery_charge" id="check_delivery_charge" value="{{$restaurantdetails->delivery_charge}}">
    
    <div class="col-md-8"> 
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
  											<?php if($product->image)
														$image = $product->image['location'];
  											?>
  										@endif

    									{{-- */ 
    								      // Product::productOptions($product['id']);      
    										  $productitemscount = Product::productOptions($product['id']);                                  
                      /* --}}

                        <!-- <div class="row" id="{{ isset($product->menu->id) ? str_replace(' ', '', $product->menu->id) : ''}}"> -->

                        <div class="row">
                            <div class="col-md-7 item-name" rel="{{$product['id']}}">
                              {{$product['name']}}
                            </div>
                            
                            <div class="col-md-3">                                          
                                @if($flag_close)                                        
                                    <span class="qty-subb"><i class="fa fa-minus-circle"></i></span>
                                  <span class="qty-inputt"><input disabled="disabled"  type="text" value="0"></span>
                                  <span class="qty-addd"><i class="fa fa-plus-circle"></i></span>
                                @else
                                  <span field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['id']}}" class="@if($productitemscount>0) {{'hasaddons'}} @else {{'noaddons'}} @endif qtyminus qty-sub" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['id']}}" @endif >
                                    <i class="fa fa-minus-circle"></i>
                                  </span>
                                  
                                  <span class="qty-input"><input name="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['id']}}" type="text" value="@if(isset($cart->getData()[$product['id']]['quantity'])) {{$cart->getData()[$product['id']]['quantity'] }} @else{{0}}@endif">
                                  </span>
                                  
                                  <span field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['id']}}" class="qtyplus qty-add @if($productitemscount>0){{'hasaddons'}}@else{{'noaddons'}}@endif" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['id']}}" @endif >
                                    <i class="fa fa-plus-circle"></i>
                                  </span>
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
                                                                            echo ($productaddon[0]['required']=="Y") ? $key."*": $key;
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
                                                                                    if($itemdetail['type']=='checkbox') { echo "name=itemaddond[]"; } else { echo "name='itemaddond[]".$key."'"; } 
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

                            <div class="col-md-2 item-price" rel="{{$product['cost']}}">
                                {{format($product['cost'])}}
                            </div>

                        </div>
                    @endforeach                              
                </div>

                <div class="clearfix">
                </div>

            </div>
					</div>
		</div>

    <div class="col-md-4">
    	<div class="panel panel-default">
    		<div class="panel-heading">{{trans('admin.panel.heading.without.title')}}</div>
    		<div class="panel-body">
    		  
          <div class="side-widget">
    				<div class="side-widget-data">
    					<!-- <div class="form-group">
    						<label>{{trans('admin.order.date')}}</label>
    						<input type='text' name='orderdatetime' class="form-control datetimepicker" />
    					</div> -->

    					@include('cart.cart')

    					<div class="radio-cont">
      						<input type="hidden" value="{{$restaurantdetails->restaurant_id}}" name="restaurant_id">
      						<input type="radio" value="delivery" name="order_type" id="radio1" class="css-checkbox" checked="checked"/>
      						<label for="radio1" class="css-label radGroup1">{{trans('admin.delivery')}}</label>
    					</div>
    					<div class="radio-cont">
      						<input type="radio" value="pickup" name="order_type" id="radio2" class="css-checkbox"/>
      						<label for="radio2" class="css-label radGroup1">{{trans('admin.pickup')}}</label>
      						<span class="help-text">{{trans('admin.you.will.pick.up.the.order.yourself.at.restaurant')}}</span>
    					</div>
    				</div>
    			</div>

          <div class="side-widget">
              <div class="side-widget-data">
                <div class="radio-cont delivery-radio-option">
                  <input type="radio" checked="checked" class="css-checkbox" id="radio3" name="asap" value="soon">
                  <label class="css-label radGroup1" for="radio3">{{trans('admin.as.soon.as.possible')}}</label>
                </div>
                <div class="radio-cont delivery-radio-option">
                  <input type="radio" class="css-checkbox later" id="radio4" name="asap" value="later">
                  <label class="css-label radGroup1" for="radio4">{{trans('admin.later')}}</label>
                </div>
                <div class="form-group later">
                    <label>{{trans('admin.order.date')}}</label>
                    <input type='text' name='orderdatetime' class="form-control datetimepicker" />
                </div>
              </div>
          </div>
          
    			<div class="side-widget-data">
    				  <p class="small-text">({{trans('admin.if.you.want.to.add.some.comment.e.g.delivery.instruction.this.is.right.place')}})</p>
    				  <div class="form-group">
    					   <textarea type="text" name="remark" value="" placeholder="add a message to your order" class="form-control icon-field">
    					   </textarea>
    					   <i class="flaticon-letter"></i>
    				  </div>
    			</div>
          
    			<div class="form-group mt20">
              <!-- <div class="row">
                <div class="col-sm-9"> -->
    				      <input type="text" name="coupancode" value="" placeholder="Enter Coupon Code" class="form-control">
                  <p class="text-danger" id="coupanmessage"></p>
                <!-- </div>
                <div class="col-sm-3 voucher-apply-btn">
                    <button type="submit" id="admincoupanvalid" class="btn btn-primary btn-lg sbmt-btn-abs">Apply</button>
                </div>
              </div> -->
    			</div>
          
          @if(!$flag_close)
      			<div class="form-group">
        				<a href="{{url($prefix.'/order')}}" class="btn btn-primary">{{trans('admin.back')}}</a>&nbsp;	
        				<!--<input type ="submit" class="checkout-btn" value="Submit">-->
        				<button id="admin_order_add" type="submit" class="btn btn-primary">{{trans('admin.submit')}}</button>
      			</div>
          @endif
    		</div>
      </div>
    </div>

  {!! Form::close() !!}

</div>

@else
    <div class="col-md-12 text-center">{{$message}}</div>
@endif