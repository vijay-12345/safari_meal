<?php

namespace App;
use DB;
use Session;

class Cart
{
	protected $handler;

	protected $status;

	public function __construct($handler = 'App\CartSession') { //App\SessionCart and App\DbCart	
		$this->handler = new $handler();
	}

	public function addtocart($inputs) {					
		try {
			$optionItemsname = '';
			$restaurantproduct = \App\Product::lang()->where('id',$inputs['prodid'])->first();					
			$itemaddond = array();
			$discount = '0';
			$coupandetails = '';
			if(isset($inputs['itemaddond'])) {
				$itemaddond = $inputs['itemaddond'];
				$optionItems = OptionItem::lang()->whereIn('id', $inputs['itemaddond'])->get();						
				foreach($optionItems as $optionItem) {
					$optionItemsname .= $optionItem->item_name."***";
				}
			}
			
			//(new Cart())->setcoupandetails(serialize($validid));
			if($restaurantproduct) {
				//write data to handler
				$this->status = $this->handler->write([
					'prodid' => $inputs['prodid'],
					'name' => $restaurantproduct['name'],
					'quantity' => $inputs['quantity'],
					'product_type' => $restaurantproduct['product_type'],
					'restaurant_id' => $restaurantproduct['restaurant_id'],
					'description' => $restaurantproduct['description'],
					'cost' => $restaurantproduct['cost'],
					'totalCost' => $this->getTotalItemPrice($restaurantproduct,$inputs['quantity'],$itemaddond),
					'field' => $inputs['field'],				
					'optionItem' => $optionItemsname,
					'itemaddond'=>$itemaddond,
				]);
			} else {
				$this->status = 'fail';
			}
			return $this->status === true ? 'success' : 'fail';
		} catch(\Exception $e) {
			// echo $e->getMessage();die;
			$this->status = $e->getMessage();
		}
	}

	
	public function getOtherData() {			
		$data = $this->handler->read();		
		return (empty($data['other'])) ? [] : $data['other'];
	}

	public function getData() {			
		$data = $this->handler->read();

		if(!empty($data['other'])) unset($data['other']);

		return $data ? $data : [];
	}

	public function getSubtotal() {
		$subtotal = 0;
		$datas =  $this->getData();
		foreach($datas as $key=>$data) {
			if(!in_array($key, array('other'))) $subtotal += $data['totalCost'];
		}
		return $subtotal;
	}
	
	public function getTotal($order_type = null)
	{
		$subtotal = $this->getSubtotal();
		
		$cgst = $this->getCGST();
		$cgst = $cgst ? $cgst / 100 * $subtotal : 0;

		$sgst = $this->getSGST();
		$sgst = $sgst ? $sgst / 100 * $subtotal : 0;

		$deliverycharge = ( is_null($order_type) || $order_type == 'pickup') ? 0 : $this->deliveryCharges();

		$total = $subtotal + $deliverycharge + $this->getPackagingFees() + $cgst + $sgst - $this->getcoupandiscountvalue();

		return $total;
	}
	
	public function getTotalItemPrice($restaurantproduct, $quantity, $itemaddond = array()) {
		$ProductPrice = DB::table('product_options')->select('price')->whereIn('option_item_id',$itemaddond)->where('product_id',$restaurantproduct['id'])->get();
		$total=0;
		foreach($ProductPrice as $key=>$val)
			$total +=	$val->price;
		return ($restaurantproduct['cost']+$total)*$quantity;	
	}

	public function deliveryCharges() {
		
		$datas =  $this->getOtherData();
		
		$subtotal = $this->getSubtotal();
		
		$cgst = $this->getCGST();
		$cgst = $cgst ? $cgst / 100 * $subtotal : 0;
		
		$sgst = $this->getSGST();
		$sgst = $sgst ? $sgst / 100 * $subtotal : 0;
		
		$subtotal = $subtotal + $this->getPackagingFees() + $cgst + $sgst - $this->getcoupandiscountvalue();
		
		if(empty($datas['deliveryChargesApplicable'])) $deliveryChargesApplicable = 0;
		
		else $deliveryChargesApplicable = $datas['deliveryChargesApplicable'];
		
		if($subtotal >= $deliveryChargesApplicable) return 0;
		else {
			if(empty($datas['deliveryCharges'])) return 0;

			else return $datas['deliveryCharges'];
		}
	}
	
	public function setdeliveryCharges($deliveryCharges = 0, $deliveryChargesApplicable=  0) {
		$this->handler->setdeliveryCharges($deliveryCharges, $deliveryChargesApplicable);		
	}

	public function setcoupandetails($coupan) {
		$this->handler->setcoupan($coupan);
	}

	public function getcoupandetails() {

		$datas =  $this->getOtherData();
		
		if(empty($datas['coupan'])) return array();

		else return unserialize($datas['coupan']);
	}

	public function getcoupandiscountvalue()
	{
		$datas =  $this->getOtherData();
		
		if(empty($datas['coupan'])) return 0;

		else $coupan = unserialize($datas['coupan']);
		
		if($coupan->type == 0) return $coupan->coupon_value;

		else return (($this->getSubtotal() * $coupan->coupon_value)/100);
	}
	
	public function getcoupanid()
	{
		$datas =  $this->getcoupandetails();

		if(empty($datas['id'])) return "";

		return $datas['id'];
	}

	public function clearcartOthercoupan(){
		$this->handler->clearcartOthercoupan();	
	}

	public function clearcart(){
		$this->handler->clearcart();	
	}
	
	public function setPackagingFees($fees = 0) {
		$this->handler->setPackagingFees( $fees );
	}

	public function getPackagingFees($fees = 0) 
	{
		$data = $this->getOtherData();
		return isset( $data['packaging_fees'] ) ? $data['packaging_fees'] : 0;
	}

	public function setCGST($percent = 0) {
		$this->handler->setCGST( $percent );
	}

	public function getCGST($fees = 0) 
	{
		$data = $this->getOtherData();
		return isset( $data['cgst'] ) ? $data['cgst'] : 0;
	}

	public function setSGST($percent = 0) {
		$this->handler->setSGST( $percent );		
	}

	public function getSGST($fees = 0) 
	{
		$data = $this->getOtherData();
		return isset( $data['sgst'] ) ? $data['sgst'] : 0;
	}

	public function setAdminCommission($percent = 0) {
		$this->handler->setAdminCommission( $percent );		
	}

	public function getAdminCommission() 
	{
		$data = $this->getOtherData();
		return isset( $data['commission'] ) ? $data['commission'] : 0;
	}

	public function setOrderType( $type ) {
		$this->handler->setOrderType( $type );
	}

	public function getOrderType() 
	{
		$data = $this->getOtherData();
		return isset( $data['order_type'] ) ? $data['order_type'] : null;
	}

}