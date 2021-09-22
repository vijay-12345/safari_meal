<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cuisine extends AppModel
{
		
	protected $table = 'cuisine';
	protected $primaryKey = 'id';
	public $timestamps = true;
	public $fillable = ['name','lang_id','status'];
	
	protected $hidden = [];
    use SoftDeletes;

    protected $dates = ['deleted_at'];	
    /**
     * The restaurant that belong to the cuisine.
     */
    public function restaurants()
    {
        return $this->belongsToMany('App\Restaurent','restaurant_cousine','restaurant_id','cousine_id');
    }
    public static function getCuisineByRestaurantId($id){ 				
			$q = Cuisine::lang()->select('cuisine.*')
			 ->Join('restaurant_cousine', 'restaurant_cousine.cousine_id', '=', 'cuisine.id')
			 ->where('restaurant_cousine.restaurant_id',$id);			 			
			$cuisines = $q->get();
			$cuisines_count = $q->count();
			return ['cuisines_count'=>$cuisines_count,'cuisines'=>$cuisines];	
		
    } 
}
?>
