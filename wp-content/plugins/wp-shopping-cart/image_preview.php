<?php
  $imagetype = @getimagesize($imagepath);
  switch($imagetype[2])
    {
    case IMAGETYPE_JPEG:
    //$extension = ".jpg";
    $src_img = imagecreatefromjpeg($imagepath);
    $pass_imgtype = true;
    break;

    case IMAGETYPE_GIF:
    //$extension = ".gif";
    $src_img = imagecreatefromgif($imagepath);
    $pass_imgtype = true;
    break;

    case IMAGETYPE_PNG:
    //$extension = ".png";
    $src_img = imagecreatefrompng($imagepath);/*
    ImageAlphaBlending($src_img, false);
    imagesavealpha($src_img,true);*/
    $pass_imgtype = true;
    break;

    default:
    $pass_imgtype = false;
    break;
    }

  if($pass_imgtype === true)
    {
    $source_w = imagesx($src_img);
    $source_h = imagesy($src_img);

 //Temp dimensions to crop image properly
    $temp_w = $width;
    $temp_h = $height;
    
	// select our scaling method
    $scaling_method = 'cropping';
    
    //list($source_h, $source_w) = array($source_w, $source_h);
    
    // set both offsets to zero
    $offset_x = $offset_y = 0;
    
    // Here are the scaling methods, non-cropping causes black lines in tall images, but doesnt crop images.
    switch($scaling_method) {
      case  'cropping':
        // if the image is wider than it is high and at least as wide as the target width. 
				if (($source_h <= $source_w)) {				  
					if ($height < $width ) {
						$temp_h = ($width / $source_w) * $source_h;
					} else {
						$temp_w = ($height / $source_h) * $source_w;
					}
						
					//$temp_w = ($height / $source_h) * $source_w;
				} else {
					$temp_h = ($width / $source_w) * $source_h;
				}
      break;    
    
      case 'non-cropping':
      default:
				if ($height < $width ) {
					$temp_h = ($width / $source_w) * $source_h;
				} else {
					$temp_w = ($height / $source_h) * $source_w;
				}
			break;
    }
    
    
//     echo "Source Height: $source_h <br />";
//     echo "Source Width: $source_w <br />";
//     echo "<br />";
//     echo "Temp Height: $temp_h <br />";
//     echo "Temp Width: $temp_w <br />";
//     exit();

 
    // Create temp resized image
    $temp_img = ImageCreateTrueColor( $temp_w, $temp_h );
    $bgcolor = ImageColorAllocate( $temp_img, 255, 255, 255 );
    ImageFilledRectangle( $temp_img, 0, 0, $temp_w, $temp_h, $bgcolor );
    ImageAlphaBlending( $temp_img, TRUE );

    ImageCopyResampled( $temp_img, $src_img, 0, 0, 0, 0, $temp_w, $temp_h, $source_w, $source_h );



    $dst_img = ImageCreateTrueColor($width,$height);
    $bgcolor = ImageColorAllocate( $dst_img, 255, 255, 255 );
    ImageFilledRectangle( $dst_img, 0, 0, $width, $height, $bgcolor );
    ImageAlphaBlending($dst_img, TRUE );
    imagecolortransparent($dst_img, $bgcolor);

    // X & Y Offset to crop image properly
    if($temp_w < $width) {
			$w1 = ($width/2) - ($temp_w/2);
    } else if($temp_w == $width) {
			$w1 = 0;
    } else {
			$w1 = ($width/2) - ($temp_w/2);
    }
    
    if($temp_h < $height) {
			$h1 = ($height/2) - ($temp_h/2);
    } else if($temp_h == $height) {
      $h1 = 0;
    } else {
			$h1 = ($height/2) - ($temp_h/2);
    }
    
    switch($scaling_method) {
      case  'cropping': 
				ImageCopy( $dst_img, $temp_img, $w1, $h1, 0, 0, $temp_w, $temp_h );
		  break;
		  
      case 'non-cropping':
      default:
				ImageCopy( $dst_img, $temp_img, 0, 0, 0, 0, $temp_w, $temp_h );
      break;
		}
    
// 		imagesavealpha($dst_img,true);
		ImageAlphaBlending($dst_img, false);
    

    header("Content-type: image/png");
    ImagePNG($dst_img);
//    ImagePNG($temp_img);
    exit();
    }
?>