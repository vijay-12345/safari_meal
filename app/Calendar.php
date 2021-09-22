<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendar extends AppModel
{
	protected $table = 'Calendar';
	
	protected $primaryKey = 'dt';
	
	public $timestamps = false;

	public $fillable = ['dt', 'y', 'q', 'm', 'd', 'dw', 'w', 'month_name', 'day_name'];
	
	protected $hidden = [];
}
