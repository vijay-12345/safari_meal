<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
	protected $table = 'image';
	
	protected $primaryKey = 'id';
	
	//public $timestamps = true;

	public $fillable = ['id','type','target_id','location','logo_location'];
	
	protected $hidden = [];		
	
	/*public function city()
	{
		return $this->hasOne('App\City','id','city_id');
	}*/
	public static function imageUpload($image,$type){
		
	    $extension = $image->getClientOriginalName(); // getting image extension	    
	    $fileName = rand(11111,99999).'.'.$extension; // renameing image
	    $path = self::getImagePath($type);	    
	    $image->move($path['rel_path'], $fileName); // uploading file to given path
        if($type=='user'){
            $thumb = self::resize(100,100,$path['rel_path'].$fileName,$image->getClientOriginalExtension());
        }
        if($type=='restaurant' || $type=='menu'){
            $thumb = self::resize(596,303,$path['rel_path'].$fileName,$image->getClientOriginalExtension());
        }        
	    return $path['full_path'].$fileName;
	    
    }
    
    public static function getImagePath($type){
    	switch ($type) {
    		case 'restaurant':
                return ['full_path'=>'http://'.$_SERVER['SERVER_NAME'].'/images/uploads/restaurant/',
                'rel_path'=>'images/uploads/restaurant/',
                'default_image'=>'http://'.$_SERVER['SERVER_NAME'].'/images/no-image.png'];
                break;
     		case 'product':
    			return ['full_path'=>'http://'.$_SERVER['SERVER_NAME'].'/images/uploads/product/',
    			'rel_path'=>'images/uploads/product/',
    			'default_image'=>'http://'.$_SERVER['SERVER_NAME'].'/images/no-image.png'];
    			break;   			
    		case 'menu':
    			return ['full_path'=>'http://'.$_SERVER['SERVER_NAME'].'/images/uploads/menu/',
    			'rel_path'=>'images/uploads/menu/',
    			'default_image'=>'http://'.$_SERVER['SERVER_NAME'].'/images/no-image.png'];
    			break;
             case 'user':
                return ['full_path'=>'http://'.$_SERVER['SERVER_NAME'].'/images/uploads/user/',
                'rel_path'=>'images/uploads/user/',
                'default_image'=>'http://'.$_SERVER['SERVER_NAME'].'/images/no-image.jpg'];
                break;                     		
    		default:
    			return false;
    			break;
    	}
    }
    /*
    * resize image
    */
    public static function resize($newwidth, $newheight,$fileName,$extension){
            // some settings
            $max_upload_width = $newwidth;
            $max_upload_height = $newheight;
            // if uploaded image was JPG/JPEG
            $extension = strtolower($extension);
            if($extension == "jpg"	 || $extension == "jpeg" || $extension == "pjpeg"){    
                $image_source = imagecreatefromjpeg($fileName);
            } 
           // echo $extension;die;      
            // if uploaded image was GIF
            if($extension == "gif"){ 
                $image_source = imagecreatefromgif($fileName);
            }   
            // BMP doesn't seem to be supported so remove it form above image type test (reject bmps)   
            // if uploaded image was BMP
            if($extension == "bmp"){ 
                $image_source = imagecreatefromwbmp($_FILES["image_upload_box"]["tmp_name"]);
            }           
            // if uploaded image was PNG
            if($extension == "png"){
                $image_source = imagecreatefrompng($fileName);
            }    
			/* // It gets the size of the image
        list( $width,$height ) = getimagesize( $uploadimage );   


        // It loads the images we use jpeg function you can use any function like imagecreatefromjpeg
        $thumb = imagecreatetruecolor( $newwidth, $newheight );
        $source = imagecreatefromjpeg( $resize_image );


        // Resize the $thumb image.
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);


        // It then save the new image to the location specified by $resize_image variable

        imagejpeg( $thumb, $resize_image, 100 ); 

        // 100 Represents the quality of an image you can set and ant number in place of 100.
           


        $out_image=addslashes(file_get_contents($resize_image));
       */
            $remote_file = $fileName;
            imagejpeg($image_source,$remote_file,100);
            chmod($remote_file,0777);
            // get width and height of original image
            list($image_width, $image_height) = getimagesize($remote_file);
        
            if($image_width>$max_upload_width || $image_height >$max_upload_height){
                $proportions = $image_width/$image_height;
                
                if($image_width>$image_height){
                    $new_width = $max_upload_width;
                    $new_height = round($max_upload_width/$proportions);
                }       
                else{
                    $new_height = $max_upload_height;
                    $new_width = round($max_upload_height*$proportions);
                }       
                
                
                $new_image = imagecreatetruecolor($new_width , $new_height);
                $image_source = imagecreatefromjpeg($remote_file);
                
                imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
                imagejpeg($new_image,$remote_file,100);
                
                imagedestroy($new_image);
            }            
            imagedestroy($image_source);

    }
    public static function deleteImage($imagePath, $type){
    	$path = Image::getImagePath($type);
    	@unlink($path['rel_path'].basename($imagePath));
    }
}
?>
