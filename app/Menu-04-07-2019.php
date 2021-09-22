<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Menu extends AppModel
{
		
	protected $table = 'menu';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['name','lang_id','status'];
	
	protected $hidden = [];
    use SoftDeletes;

    protected $dates = ['deleted_at'];
     /**
     * Get the image record associated with the user.
     */  	
	public function image()
	{
		return $this->hasOne('App\Image','target_id','id')
		->where('type',\Config::get('constants.image.for.menu'));
	}
}
?>
