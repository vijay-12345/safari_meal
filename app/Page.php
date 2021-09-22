<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Page extends AppModel
{
	protected $table = 'page';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['id','title','description','page_urlalias','status','lang_id'];
    use SoftDeletes;

    protected $dates = ['deleted_at'];	

    //validation rule
	public static $rules = [
    'create' => [
        'title'		=> 'required',
        'description' 		=> 'required',        
       
        ],
    'edit'   => [
        'title'     => 'required',
        'description'       => 'required'

        ]
     ]; 	   
}
?>
