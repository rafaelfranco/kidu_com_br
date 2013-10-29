<?php
	/**
	 * Project: SIMPLE PHP - Framework 
	 * 
	 * @copyright RFTI  www.rfti.com.br
	 * @author Rafael Franco <rafael@rfti.com.br>
	 */

	/**
	 * email module
	 *
	 * @package email
	 * @author Rafael Franco
	 **/
	class email
	{
		public function __construct() 
		{
			
		}
		/**
		* send emails email
		* @param <string> $contents
		* @param <int> $get_attributes
		* @return <array >
		*/
		public function send($email,$subject,$html,$from='SimplePhp',$fromEmail='email@simplephp.org') {

	        $headers  = 'MIME-Version: 1.0' . "\r\n";
	        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	        $headers .= 'To: '.$email. "\r\n";
	        $headers .= 'From: '.$from.'<'.$fromEmail.'>' . "\r\n";

	        mail($email, $subject, $html,$headers);
	
	        return true;
	    }
	}
?>
