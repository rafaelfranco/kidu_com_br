<?php
/**
 * Project: SIMPLE PHP - Framework 
 * 
 * @copyright RFTI  www.rfti.com.br
 * @author Rafael Franco <rafaelfranco@me.com>
 */

#development
if($_SERVER['HTTP_HOST'] == DEVEVOPMENT_URL) {
    $dsn = array(
                'phptype'  => 'mysql',
                'username' => 'root',
                'password' => '',
                'hostspec' => '127.0.0.1',
                'database' => 'footbooking_com_br',
            );
     
}

#tests
if($_SERVER['HTTP_HOST'] == TEST_URL) {
          $dsn = array(
                'phptype'  => 'mysql',
                'username' => 'root',
                'password' => '',
                'hostspec' => '127.0.0.1',
                'database' => 'footbooking_com_br',
            );
}

#production
if($_SERVER['HTTP_HOST'] == PRODUCTION_URL) {
           $dsn = array(
	                'phptype'  => 'mysql',
	                'username' => 'root',
	                'password' => 'oXP3mmK4etHkLg',
	                'hostspec' => '127.0.0.1',
	                'database' => 'footbooking_com_br',
	            );
} 

$options = array(
        'debug'       => 2,
        'portability' => MDB2_PORTABILITY_ALL,
);
  
$mdb2 =& MDB2::connect($dsn, $options);

if (PEAR::isError($mdb2)) {
    die('error:'.$mdb2->getMessage());
}
    
?>
