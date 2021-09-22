<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends AppModel
{
	protected $table = 'review';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['id', 'review', 'rating', 'date', 'customer_id', 'restaurant_id', 'status'];
    
    public function getDateAttribute($value)
    {
        return date('Y-m-d', strtotime($value));
    }

    //validation rule
	public static $rules = [
        'create' => [
            'review'		=> 'required',
            'rating' 		=> 'required',
            'customer_id' 	=> 'required',
            'restaurant_id'   		=> 'required',
            'date'=>'required'        
            ],
        'edit'   => [
            'review'		=> 'required',
            'rating' 		=> 'required',
            'customer_id' 	=> 'required',
            'restaurant_id' => 'required',
            'date'=>'required'    
        ]
    ];

    public function restaurants()
    {
        return $this->belongsTo('App\Restaurent','restaurant_id','id');
    }

    public function customer()
    {
        return $this->belongsTo('App\User', 'customer_id', 'id');
    }

    /*===date format======*/
    public static function dateFormat(&$input,$format = 'Y-m-d')
    {
 		if( isset( $input['date'] ) )
        {
			$date = date_create( $input['date'] );
			$input['date'] = date_format($date, $format);
 		}
    }
}
