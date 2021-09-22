<?php 
namespace App;

use Session;

class CartSession
{
	public function write($data) 
	{
		foreach($data as $key => $val) {
			Session::put('cart.'.$data['prodid'].'.'.$key, $val);
		}
		
		if( $data['quantity'] < 1 ) {
			Session::pull('cart.'.$data['prodid']);			
		}
		
		return true;
	}
	
	public function read() {
		return Session::get('cart');
	}
	
	public function clearcart()
	{
		Session::pull('cart');
		Session::pull('Errormessage');
		Session::pull('Successmessage');
		
		Session::put('cart', array());
        // Session::save();
	}
	
	public function clearcartOthercoupan()
	{
		Session::pull('cart.other.coupan');	
	}
	
	public function setcoupan($data) {
		Session::put('cart.other.coupan', $data);
	}
	
	public function setdeliveryCharges($deliveryCharges, $deliveryChargesApplicable) {
		Session::put('cart.other.deliveryCharges', $deliveryCharges);				
		Session::put('cart.other.deliveryChargesApplicable', $deliveryChargesApplicable);				
	}
	
	public function setdiscount($data) {			
		Session::put('cart.other.discount', $data);						
	}
	
	public function setPackagingFees($fees = 0) {
		Session::put('cart.other.packaging_fees', $fees);		
	}
	
	public function setCGST($percent = 0) {
		Session::put('cart.other.cgst', $percent);		
	}

	public function setSGST($percent = 0) {
		Session::put('cart.other.sgst', $percent);
	}

	public function setAdminCommission($percent = 0) {
		Session::put('cart.other.commission', $percent);
	}
	
	public function setOrderType( $type ) {
		Session::put('cart.other.order_type', $type);
	}
	
}