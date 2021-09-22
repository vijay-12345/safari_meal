<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Timing extends AppModel
{
	protected $table = 'timing';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','open','closing','weekday','restaurant_id','delivery_start','delivery_end'];
}
?>
