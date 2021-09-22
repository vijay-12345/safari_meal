<?php  
    use App\Product;
    //$cart = new App\Cart; 
    $subtotal = $cart->getSubtotal();
    if(isset($deliveryOption) && $deliveryOption=='delivery')
        $total = $cart->getTotal('delivery');
    else $total = $cart->getTotal();
?>
<?php
  foreach($cart->getData() as $key => $product) {     
  	if(in_array($key, array('other')))
  		  $otherdetails = $product;
  	else {
		  $productitemscount = Product::productOptions($product['prodid']);                                                                  
?>
    
    <div class="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['prodid']}} summry-prod-single menu-item-data">
  	  
      <input type="hidden" class="checkoutproductid" value="{{$product['prodid']}}" name="checkoutproductid">

      <div class="row">                   
        <div class="col-md-7">
          <div class="p-title">
            <a title="" href="#">{{$product['name']}}</a>
          </div>
        </div>
        
        <div class="col-md-5">
          <div class="row">
            <div class="col-sm-6">

              <span rel="checkout" field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['prodid']}}" class="@if($productitemscount>0){{'hasaddons'}}@else{{'noaddons'}}@endif qtyminus qty-sub" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['prodid']}}" @endif >
                <i class="fa fa-minus-circle"></i>
              </span>

              <span class="qty-input">
                <input readonly name="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['prodid']}}" type="text" value="{{$product['quantity']}}">
              </span>

              <span rel="checkout" field="{{str_replace(' ', '', $product['name'])}}Qty-{{$product['prodid']}}" class="qtyplus qty-add @if($productitemscount>0){{'hasaddons'}}@else{{'noaddons'}}@endif" @if($productitemscount>0) data-toggle="modal" data-target="#ProdAddon{{$product['prodid']}}" @endif >
                <i class="fa fa-plus-circle"></i>
              </span>
                
              <div class="modal fade" id="ProdAddon{{$product['prodid']}}" role="dialog">
                <div class="modal-dialog">    
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">{{$product['name']}}</h4>
                    </div>
                    <div class="modal-body">                                                       
                      {{-- */ 
                        $productaddons = Product::productAddons($product['prodid']);                                                          
                      /* --}}
                      <div class="content-block choices-toppings__container">                                                                
                        {!! Form::open(array('class' =>'orderitemaddons')) !!} 
                          
                          <input type="hidden" name="productid[]" value="{{$product['prodid']}}">
                          <input type="hidden" name="itemname" value="{{$product['name']}}">
                          <input type="hidden" name="itemprice" value="{{$product['cost']}}">                                               

                          <div class="choices-toppings__elements__wrapper choices-toppings__product__information toggle-elements-container choices-toppings__product__information--no-image">
                              <table class="table-cart">
                                <tbody>
                                  <tr>
                                    <td class="choices-toppings__product__information__text">
                                      <label for="cart_product_skeleton_quantity" class="control-label required">
                                        Quantity
                                      </label>
                                    </td>
                                    <td class="choices-toppings__product__information__quantity">                               
                                      <div class="choices-toppings__product__information__text__description">
                                      </div>
                                      <input type="number" min="0" value="<?=isset($cart->getData()[$product['prodid']]['quantity'])?$cart->getData()[$product['prodid']]['quantity']:1 ?>" class="form-control number" name="cart_product_quantity" >
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
                                    <?php echo ($productaddon[0]['required']=="Y")?$key."*":$key;
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
                                              if(isset($cart->getData()[$product['prodid']]['itemaddond']) && in_array($itemdetail['option_item_id'],$cart->getData()[$product['prodid']]['itemaddond']))
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
                            <button rel = "checkout" class="btn btn-primary btn-lg choices-toppings__button--submit" name="cart_product_skeleton[submit]" id="cart_product_skeleton_submit" type="button">Submit</button>
                          </div>                                                            
                        {!! Form::close() !!}

                      </div>                                                    
                                                                                
                    </div>                                                       
                  </div>      
                </div>
              </div>

            </div>

            <div class="col-sm-6">
              <h5 class="p-price">{{format($product['totalCost'])}}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>

<?php
    }
  }
?>
  
  <div class="cart-total-summry">
      <div class="row">
          <div class="col-md-5 col-md-offset-7">

              <div class="row">
                  <div class="col-sm-9">
                    <div class="cart-total-title text-right">Subtotal:</div>
                  </div>
                  <div class="col-sm-3">
                    <div class="cart-total-title text-center">{{format($subtotal)}}</div>
                  </div>
              </div>

              <!-- start commented in old code -->
              <div class="row">
                  <div class="col-sm-9">
                    <div class="cart-total-title text-right">CGST {{ $cart->getCGST() }} %:</div>
                  </div>
                  <div class="col-sm-3">
                    <div class="cart-total-title text-center">{{ format($cart->getCGST()/100 * $subtotal) }}</div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-9">
                    <div class="cart-total-title text-right">SGST {{ $cart->getSGST() }} %:</div>
                  </div>
                  <div class="col-sm-3">
                    <div class="cart-total-title text-center">{{ format($cart->getSGST()/100 * $subtotal) }}</div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-9">
                    <div class="cart-total-title text-right">Packaging Fees:</div>
                  </div>
                  <div class="col-sm-3">
                    <div class="cart-total-title text-center">{{ format($cart->getPackagingFees()) }}</div>
                  </div>
              </div>
              <!-- end commented in old code -->
              
              <div class="row" id="delivery" style="display:<?=($deliveryOption=='pickup') ? 'none' :'block';?>;">
                  <div class="col-sm-9">
                    <div class="cart-total-title text-right">
                      {{Lang::get('proceedtocheckout.Delivery.Charges')}}:
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div id="deliveryChargeVal" data-total="{{ $cart->deliveryCharges() }}" class="cart-total-title text-center">
                      {{format($cart->deliveryCharges())}}
                    </div>
                  </div>
              </div>
              
              @if(!empty($cart->getOtherData()['coupan']))
                <div class="row green-color">
                  <div class="col-sm-9">
                    <div class="cart-total-title text-right">Coupan Discount:</div>
                  </div>
                  <div class="col-sm-3">
                    <div class="cart-total-title text-center">{{format($cart->getcoupandiscountvalue())}}</div>
                  </div>
                </div>
              @endif

              <div class="row">
                <div class="col-sm-9">
                  <div class="cart-total-title text-right">Total:</div>
                </div>
                <div class="col-sm-3">
                  <div class="cart-total-title text-center" id="total" data-total="{{ $total }}">{{format( $total )}}</div>
                </div>
              </div>

              <!-- <div class="row green-color">
                  <div class="col-sm-6">
                    <div class="cart-total-title text-right">Delivery Charges:</div>
                  </div>
                  <div class="col-sm-6">
                    <div class="cart-total-title text-center">{{format($cart->deliveryCharges())}}</div>
                  </div>
              </div> -->

          </div>
      </div>
  </div>
  
  <div class="redeem-coupan">
      <div class="row">
          <!-- <h5>{{Lang::get('proceedtocheckout.Enter.voucher.code.here')}}</h5> -->
          @if(empty($cart->getOtherData()['coupan']))
            {!! Form::open(array('url' => 'checkcoupanvalid')) !!}
              <div class="col-md-10">
                <h5>{{Lang::get('proceedtocheckout.Enter.voucher.code.here')}}</h5>
                <div class="form-group">
                    <input type="text" name="coupancode" value="" placeholder="{{Lang::get('proceedtocheckout.Enter.voucher.code')}}" class="form-control icon-field">
                    <i class="flaticon-interface-3 text-danger" id='coupanmessage'></i>
                    <button type="submit" id="checkcoupanvalid" class="coupon-submit btn btn-primary btn-lg sbmt-btn-abs">Submit</button>
                </div>
              </div>
            {!! Form::close() !!}
          @else
            <div class="col-md-10">
              <h5>Enter voucher code here</h5>
              <div class="form-group">
                <input type="text" readonly name="coupancode" value="" placeholder="Enter voucher code" class="form-control icon-field">
                <i class="flaticon-interface-3" id='coupanmessage'></i>
                <button type="submit" disabled="true" class="btn btn-primary btn-lg sbmt-btn-abs">{{Lang::get('proceedtocheckout.Submit')}}</button>
              </div>
            </div>
          @endif

          <!-- <div class="col-md-10">
            <h5>Enter voucher code here</h5>
            <div class="form-group">
                <input type="text" class="form-control icon-field" placeholder="Enter voucher code" value="" name="">
                <i class="flaticon-interface-3"></i>
                <button class="btn btn-primary btn-lg sbmt-btn-abs" type="button">Submit</button>
            </div>
          </div> -->

      </div>
  </div>

  <div class="g-total">
      <div class="row">
          <div class="col-md-5 col-md-offset-7">
            <h4>
              {{Lang::get('proceedtocheckout.Total')}}:  
              <span id="net">
                <?php echo format( $total ); ?>
              </span>
            </h4>
            <!-- <h4>Total:  {{ format($cart->getTotal()) }}</h4> -->
          </div>
      </div>
  </div>