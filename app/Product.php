<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
//use Intervention\Image\Facades\Image as Image;

class Product extends AppModel
{
	use SoftDeletes;

	protected $table = 'product';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;
	
	public $fillable = ['id','menu_id', 'name', 'restaurant_id', 'description', 'cost', 'is_veg', 'rating', 'option_id', 'lang_id','status'];
	
	protected $hidden = [];		
    
    protected $dates = ['deleted_at'];
	
	public function image()
	{
		return $this->hasOne('App\Image', 'target_id', 'id')->where('type', \Config::get('constants.image.for.product'));
	}

	public function restaurant()
	{
		return $this->hasOne('App\Product','restaurant_id','id');
	}

	public function orderAddons($order_id)
	{
		return $this->belongsToMany('App\OptionItem','order_addons_item','product_id','addon_id')
			->withPivot('addon_quantity','price')
			->where('order_id',$order_id);
	}

	public function addonsOptions()
	{
		return $this->hasMany('App\ProductOption','product_id','id');			
	}

	public function menu()
	{
		return $this->belongsTo('App\Menu','menu_id','id');
	}

	public static function productAddons($productId)
	{
		$itemsgroupby = array();
		$productoptions = DB::table('product_options')
		->leftJoin('option_item', 'product_options.option_item_id', '=', 'option_item.id')                                                                
		->leftJoin('option_group', 'option_group.id', '=', 'option_item.option_group_id')                                                                
		->orderBy('option_group_id','ASC')	
		->select('product_options.*','option_item.*','option_group.*')	
		->where('product_options.product_id',$productId) 
		->where(["option_item.lang_id"=>trim(\Config::get('app.locale_prefix'))])
		->where(["option_group.lang_id"=>trim(\Config::get('app.locale_prefix'))])
		->get();
		foreach($productoptions as $productoption){
			$itemsgroupby[] = (array)$productoption;
		}			
		$itemsgroup_by = self::array_group_by($itemsgroupby, 'name');
		//pr($itemsgroup_by);exit;
		return $itemsgroup_by;
	}

	public static function productOptions($productId){				
		$productoptions = DB::table('product_options')->where('product_options.product_id',$productId)->count();
		return $productoptions;		
	}

	public static function array_group_by($arr, $key){
		if (!is_array($arr)) {
			trigger_error('array_group_by(): The first argument should be an array', E_USER_ERROR);
		}
		if (!is_string($key) && !is_int($key) && !is_float($key)) {
			trigger_error('array_group_by(): The key should be a string or an integer', E_USER_ERROR);
		}
		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($arr as $value) {
			$grouped[$value[$key]][] = $value;
		}
		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2) {
			$args = func_get_args();
			foreach ($grouped as $key => $value) {
				$parms = array_merge([$value], array_slice($args, 2, func_num_args()));
				$grouped[$key] = call_user_func_array('array_group_by', $parms);
			}
		}
		return $grouped;
	}

	public static function getProductById($productId){
		$product = DB::table('product')->select('*')->where('id',$productId)->first();
		return $product;
	}
}
?>
