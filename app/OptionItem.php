<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OptionItem extends AppModel
{
	protected $table = 'option_item';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
	public $fillable = ['id','item_name','option_group_id','lang_id','status'];
	public function group(){
		return $this->belongsTo('App\OptionGroup','option_group_id','id');
	}
}
?>
