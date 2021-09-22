<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class DriverDecline extends AppModel
{
	protected $table = 'driver_decline';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id','driver_id','order_id','decline'];
 	   
}
?>
