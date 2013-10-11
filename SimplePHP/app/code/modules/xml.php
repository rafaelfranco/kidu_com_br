<?php
	/**
	 * Project: SIMPLE PHP - Framework 
	 * 
	 * @copyright RFTI  www.rfti.com.br
	 * @author Rafael Franco <rafael@rfti.com.br>
	 */

	/**
	 * xml module
	 *
	 * @package xml
	 * @author Rafael Franco
	 **/
	class xml
	{
		public function __construct() 
		{
			
		}
		/**
		* Convert xml in array
		* @param <string> $contents
		* @param <int> $get_attributes
		* @return <array >
		*/
		
	     public function cdata($value) {
	         return '<![CDATA[ '.$value.' ]]>';
	    }
	
		public function xml2array($contents, $get_attributes = 1) {

			if(!$contents) return array();

			if(!function_exists('xml_parser_create')) {
				return array();
			}

			//Get the XML parser of PHP - PHP must have this module for the parser to work
			$parser                                             = xml_parser_create();
			xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
			xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
			xml_parse_into_struct( $parser, $contents, $xml_values );
			xml_parser_free( $parser );

			if(!$xml_values) return;//Hmm...
			//Initializations

			$xml_array                                          = array();
			$parents                                            = array();
			$opened_tags                                        = array();
			$arr                                                = array();

			$current                                            = &$xml_array;

			//Go through the tags.

			foreach($xml_values as $data) {
				unset($attributes,$value);//Remove existing values, or there will be trouble
				extract($data);//We could use the array by itself, but this cooler.

				$result                                            = '';

				if($get_attributes) {//The second argument of the function decides this.
					$result                                           = array();
					if(isset($value)) $result['value']                = $value;

					//Set the attributes too.
					if(isset($attributes)) {
						foreach($attributes as $attr                     => $val) {
							if($get_attributes == 1) $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
						}
					}
					} elseif(isset($value)) {
						$result                                          = $value;
					}



					//See tag status and do the needed.

					if($type == "open") {//The starting of the tag '<tag>'
					$parent[$level-1]                                 = &$current;

					if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
						$current[$tag]                                   = $result;
						$current                                         = &$current[$tag];
						} else { //There was another element with the same tag name
							if(isset($current[$tag][0])) {
								array_push($current[$tag], $result);
								} else {
									$current[$tag]                                = array($current[$tag],$result);
								}
								$last                                          = count($current[$tag]) - 1;
								$current                                       = &$current[$tag][$last];
							}

							} elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
							//See if the key is already taken.
							if(!isset($current[$tag])) { //New Key
								$current[$tag]                                 = $result;
								} else { //If taken, put all things inside a list(array)
									if((is_array($current[$tag]) and $get_attributes == 0)//If it is already an array...
									or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
										array_push($current[$tag],$result); // ...push the new element into that array.
										} else { //If it is not an array...
											$current[$tag]                              = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
										}
									}
									} elseif($type == 'close') { //End of tag '</tag>'
									$current                                      = &$parent[$level-1];
								}
							}

							if(!empty($xml_array['root']['node']['id'])) {
								$return['root']['node'][0]                     = $xml_array['root']['node'];
								} else {
									$return                                       = $xml_array;
								}
								return($return);

							}
							
							/**
					            *Convert array to xml
					            * @param <array> $data
					            * @param <boolean> $head
					            * @return string
					            */
					           public function array2xml($data,$head = true) {
					                if($head) {
					                    $return = '<?xml version="1.0" encoding="UTF-8"?><root>';
					                } else {
					                    $return ='';
					                }
					                foreach ($data as $item) {
					                     $return .="<node>";
					                     foreach ($item as $key =>$value) {
					                            $value = str_replace('&nbsp;', ' ', $value);
					                            $value = str_replace('&', '|', $value);
					                            if(is_array($value)) {
					                                    $return .="<$key>".$this->array2xml($value,false)."</$key>";
					                            } else {
					                                  $return .="<$key>".utf8_encode($value)."</$key>";
					                            }
					                     }
					                       $return .="</node>";
					                }
					                if($head) {
					                        $return .="</root>";
					                }
					                return $return;
					        }
                                                
                                                 public function array2Googlexml($data,$head = true) {
					                if($head) {
					                    $return = '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"><channel><title>Fashionera</title><description>Fashionera seu buscador de moda</description><link>http://www.fashionera.com.br</link>';
					                } else {
					                    $return ='';
					                }
					                foreach ($data as $item) {
					                     $return .="<item>";
					                     foreach ($item as $key =>$value) {
					                            $value = str_replace('&nbsp;', ' ', $value);
					                            $value = str_replace('&', '|', $value);
					                            if(is_array($value)) {
					                                    $return .="<$key>".$this->array2xml($value,false)."</$key>";
					                            } else {
					                                  $return .="<$key>".($value)."</$key>";
					                            }
					                     }
					                       $return .="</item>";
					                }
					                if($head) {
					                        $return .="</channel></rss>";
					                }
					                return $return;
					        }
					
							
					
							
	}
?>
