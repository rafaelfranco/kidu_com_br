<?php
require_once 'System.php';
require_once 'MDB2.php';
var_dump(class_exists('System', false));


$dsn = array(
                'phptype'  => 'mysql',
                'username' => 'root',
                'password' => '',
                'hostspec' => '127.0.0.1',
                'database' => 'footbooking_com_br',
            );


$options = array(
        'debug'       => 2,
        'portability' => MDB2_PORTABILITY_ALL,
);
echo 'x';
$mdb2 =& MDB2::connect($dsn, $options);

echo 'x';

if (PEAR::isError($mdb2)) {
    die('error:'.$mdb2->getMessage());
}
?>