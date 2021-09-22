<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends AppModel
{
	protected $table = 'order_status';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['id','order_id','user_id','type','remark'];
	
	protected $hidden = [];


}
?>
