<?php
   	require_once('config.php');
    require_once('enlang.php');
    $error_set  = Array();
    $message_set  = Array();
    $args = Array();
    $connection = mysql_connect(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD);
    mysql_set_charset('utf8');
   	   
    (!$connection) ? $errorSet[] = ERROR_mysql_connect : mysql_select_db(DB_NAME);
    
    /*$q = mysql_query('SELECT 'house_id\')
      **                FROM orders;
                      ');
	*/
    var_dump($q);
?>
