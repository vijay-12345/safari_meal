<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payout extends AppModel
{
	protected $table = 'payout_history';
	
	protected $primaryKey = 'id';
	
	public $timestamps = false;

	public $fillable = ['restaurant_id', 'amount', 'json'];
	
	protected $hidden = [];
	
	public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function getJsonAttribute( $value) 
    {
    	return json_decode( $value );
    }

    public function setJsonAttribute( $value ) 
    {
    	if( is_array( $value ) ) {
    		$this->attributes['json'] = json_encode( $value );
    	}

    	$this->attributes['json'] = $value;
    }
}
