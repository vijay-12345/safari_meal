<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $table = 'country';
	
	protected $primaryKey = 'country_id';
	
	public $timestamps = true;

	public $fillable = ['country_id','country_code','country_name'];
	
	protected $hidden = [];		
	
	/*public function getuser()
	{
		return $this->hasOne('App\User','user_id','id');
	}*/
}
?>
