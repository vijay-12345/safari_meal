<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends AppModel
{
	protected $table = 'product_options';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;
	
	public $fillable = ['id','product_id','	option_item_id','price'];
	public function products(){
		$this->belongsTo('App\Product','product_id','id');
	}
}
?>
