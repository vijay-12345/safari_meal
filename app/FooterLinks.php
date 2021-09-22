<?php namespace App;
use Illuminate\Database\Eloquent\Model;


class FooterLinks extends AppModel
{

	protected $table = 'footer_links';
	
	protected $primaryKey = 'id';
	public $timestamps = false;

	public $fillable = ['id','name','url','item_type','lang_id','sort','status'];
	
	protected $hidden = []; 
	
}

