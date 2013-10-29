<?php
	/**
	 * Project: SIMPLE PHP - Framework 
	 * 
	 * @copyright RFTI  www.rfti.com.br
	 * @author Rafael Franco <rafael@rfti.com.br>
	 */

	/**
	 * image module
	 *
	 * @package image
	 * @author Rafael Franco
	 **/
	class image
	{
		public function __construct() 
		{
			
		}
		public function reduceImage($original,$lado='') {  
                 $ext = explode('.', $original);
                 $extension = strtolower($ext[(count($ext)-1)]);

                  if($extension == 'png') {
                        $pic = imagecreatefrompng($original);
                 }
                 if(($extension == 'jpg') or ($extension == 'jpeg')) {
                        $pic = imagecreatefromjpeg($original);
                 }
                 if($extension == 'gif')  {
                        $pic = imagecreatefromgif($original);
                 }
                $maiorLado = ($lado == '') ? 250 : $lado;
                $qualidade = 80;

                if ($pic) {
					$width_old = imagesx($pic);
					$height_old = imagesy($pic);
					$width = imagesx($pic);
					$height = imagesy($pic);

					if( $width > $maiorLado ){
						$height = ($maiorLado / $width) * $height;
						$width = $maiorLado;
					}
					if( $height > $maiorLado ){
						$width = ($maiorLado / $height) * $width;
						$height = $maiorLado;
					}
					$thumb = imagecreatetruecolor($width, $height) or die ("Can't create Image!");
					imagecopyresampled($thumb, $pic, 0, 0, 0, 0, $width, $height, $width_old, $height_old);

                         if($extension == 'png') {
                                imagepng($thumb, $original);
                         }
                         if(($extension == 'jpg') or ($extension == 'jpeg')) {
                                ImageJPEG ($thumb, $original, $qualidade);
                         }
                         if($extension == 'gif')  {
                               imagegif($thumb, $original);
                         }
	}

        }
	
		public function createThumb($original,$maiorLado = 100) {

			$original = str_replace('http://'.$_SERVER['HTTP_HOST']."/", '',$original);

			$ext = explode('.', $original);
			
			$extension = strtolower($ext[(count($ext)-1)]);
			
			$f = explode('/', $original);

                         $resized = "upload_files/small/".md5(rand(0,111111111).time()).'_av'.'.'.$extension;
            

			if($extension == 'png') {
				$pic = imagecreatefrompng($original);
			}
			if(($extension == 'jpg') or ($extension == 'jpeg')) {
				$pic = imagecreatefromjpeg($original);
			}
			if($extension == 'gif')  {
				$pic = imagecreatefromgif($original);
			}
			$qualidade = 100;

			if ($pic) {
				$width_old = imagesx($pic);
				$height_old = imagesy($pic);
				$width = imagesx($pic);
				$height = imagesy($pic);

				if( $width > $maiorLado ){
					$height = ($maiorLado / $width) * $height;
					$width = $maiorLado;
				}
				if( $height > $maiorLado ){
					$width = ($maiorLado / $height) * $width;
					$height = $maiorLado;
				}
				$thumb = imagecreatetruecolor($width, $height) or die ("Can't create Image!");
				imagecopyresampled($thumb, $pic, 0, 0, 0, 0, $width, $height, $width_old, $height_old);

				if($extension == 'png') {
					imagepng($thumb, $resized);
				}
				if(($extension == 'jpg') or ($extension == 'jpeg')) {
					ImageJPEG ($thumb, $resized, $qualidade);
				}
				if($extension == 'gif')  {
					imagegif($thumb, $resized);
				}
			}
			return $resized;

		}
	}
?>
