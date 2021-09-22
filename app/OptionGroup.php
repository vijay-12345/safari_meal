<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OptionGroup extends AppModel
{
	protected $table = 'option_group';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;
    use SoftDeletes;

    protected $dates = ['deleted_at'];	
	public $fillable = ['id','name','type','required','lang_id','status'];
	public function options(){
		$this->hasMany('App\OptionItem','option_group_id','id');
	}
}
?>
