<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
	protected $table = 'state';
	
	protected $primaryKey = 'state_id';
	
	public $timestamps = false;

	public $fillable = ['state_id','state_name','country_id'];
	
	protected $hidden = [];		
	
	public static function getStateById($id)
	{
			$state	= State::where('state_id',$id)->first();
			return $state; 	
	}
	public static function insertGetId($data){		
		self::create($data);
		return \DB::getPdo()->lastInsertId();
	}	
}
?>
