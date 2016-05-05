<?php
   	require_once('../config/config.php');
    switch($_POST['lang']) {
        case 'es': require_once('../config/eslang.php'); break;
        default: require_once('../config/enlang.php'); break;
    }
    
    $connection = mysql_connect(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD);

    if (!$connection) 
    {
        $errorSet[] = ERROR_mysql_connect; 
    }
    else 
    {
        mysql_select_db(DB_NAME);
        mysql_set_charset('utf8');
    }
    
    $q = mysql_query('SELECT order_id as orderId
                      FROM orders
                      WHERE MD5(order_id) = \'' . $_GET[md] . '\' 
                      ;');
    
    if ($row = mysql_fetch_assoc($q))
    {
        $q = mysql_query('UPDATE orders SET is_active = 1 WHERE order_id = ' . $row['orderId'] . ' LIMIT 1 ;');
        echo 'Order was activated';
    }
    else
    {
        echo 'ORDER haven\'t been found';
    }   
    
    
?>
