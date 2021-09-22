<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class NewsLetter extends AppModel
{
	protected $table = 'newsletter';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['id','email','status'];
}
?>
