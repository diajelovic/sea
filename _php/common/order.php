<?php
    header("Content-Type: text/xml; charset=utf-8");
	require_once('../config/config.php');
    switch($_POST['lang']) {
        case 'es': require_once('../config/eslang.php'); break;
        default: require_once('../config/enlang.php'); break;
    }
	require_once('../recaptcha/recaptchalib.php');
    $error_set  = Array();
	$error_captcha = '';
    $message_set  = Array();
    $args = Array();
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
    
    /*
     * Функция проверки наличая домов 
     * Автор Андрей Борха
     * */
    function check_houses_info ($houseId)
    {
        $result = false;
        
        $q = mysql_query('SELECT    house_id as houseId, 
                                    house_name as houseName 
                        FROM houses
                        WHERE house_id = ' . (int)$houseId . ';');
        
        if (mysql_fetch_assoc($q))
        {
            $result = true;
        }

        return $result;
    }
    
    /*
     * Функция получения названия Дома
     * Автор Андрей Борха
     * */
    function get_house_name ($houseId)
    {
        $result = '';
        
        $q = mysql_query('SELECT    house_id as houseId, 
                                    house_name as houseName 
                        FROM houses
                        WHERE house_id =' . (int)$houseId . ';');
        
        if ($row = mysql_fetch_assoc($q))
        {
            $result = $row['houseName'];
        }
        else
        {
        	$result = ERROR_house;
        }

        return $result;
    }
    
    /*
     * Функция проверки занятой даты 
     * Автор Андрей Борха
     * */
    function check_order_date ($date)
    {
        $result = true;
        
        $q = mysql_query('SELECT *
                        FROM orders
                        WHERE is_active = 1 AND begin_date <= \'' . $date . '\' AND end_date >= \'' . $date . '\';');
        
        if (mysql_fetch_assoc($q))
        {
            $result = false;
        }

        return $result;
    }
    
    /*
     * Функция преобразования массивов сообщений в формат XML 
     * Автор Андрей Борха
     * */
    function result_to_xml ($error_set, $message_set, $captcha)
    {
        $result =  '<?xml version="1.0" encoding="UTF-8"?>
        ';
		$result .= '<result>';
        if (count($error_set) > 0)
        {
        	$result .= '<resultcode>0</resultcode>';
            $result .= '<errorset>';
            foreach ($error_set as $key => $value)
            {
                $result .= '<error>' . (int)++$key . '. ' . $value . '</error>';
            }
            $result .= '</errorset>';
        }
        else
        {
        	$result .= '<resultcode>1</resultcode>';
            $result .= '<messageset>';
            foreach ($message_set as $key => $value)
            {
                $result .= '<message>' . (int)++$key . '. ' . $value . '</message>';
            }
            $result .= '</messageset>';
        }
		
		$result .= '<captcha><![CDATA[' . $captcha . ']]></captcha>
		</result>';

        echo $result;
        exit;
    }

//	function normalize_linebreaks($text) {
//		$text = str_replace("\r\n", "\n", $text); /* win -> un*x */
//		$text = str_replace("\r", "\n", $text); /* mac -> un*x */
//		return $text;
//	}
    
	/*
     * Функция функция отпарвки письма на почту заказчику на Английском
     * Автор Андрей Борха
     * */
    function mail_user_eng($args)
    {
    	$message = 'Hello ' . $args['first_name'] . ' ' . $args['last_name'] . "!\n\n";
        $message .= 'Your order #' . $args['orderId'] . " was added.\n";
        $message .= 'House - ' . get_house_name($args['houseId']) . ";\n";
        $message .= 'Date arrive - ' . $args['date_arrive'] . ";\n";
        $message .= 'Date departure - ' . $args['date_departure'] . ";\n";
        $message .= "Wait till Admin submit it.\n\n";
        $message .= 'Best regards SeaAndhouse.com.';
        
        mail($args['email'], 'order #' . $args['orderId'], $message, "From: SeaAndHouse.com \r\n"."X-Mailer: PHP/" . phpversion());
    }
    
    /*
     * Функция функция отпарвки письма на почту заказчику на Английском
     * Автор Андрей Борха
     * */
    function mail_admin($args)
    {
        $message = 'Order #' . $args['orderId'] . " was added.\n\n";
        $message .= 'House - ' . get_house_name($args['houseId']) . ";\n";
        $message .= 'Date arrive - ' . $args['date_arrive'] . ";\n";
        $message .= 'Date departure - ' . $args['date_departure'] . ";\n";
        $message .= 'Comment - ' . $args['comment'] . ";\n\n";
        $message .= 'Commit Link: ' . SITE_NAME . '/src/mailconfirm.php?md=' . md5($args['orderId']);
        
        mail(ADMIN_EMAIL, 'order #' . $args['orderId'], $message, "From: SeaAndHouse.com \r\n"."X-Mailer: PHP/" . phpversion());
    }
    
    /*
     * Функция проверки правильности заполненых полей в  форме заказа
     * Автор Андрей Борха
     * */
    function form_validate() 
    {
        global $error_set;
        global $error_captcha;
        $result = array();
        
        if ($_POST['houseId'] == '')
        {
            $error_set[] = ERROR_house;
        }
        else
        {
            if (check_houses_info($_POST['houseId']))
            {
                $result['houseId'] = $_POST['houseId'];
            }
            else
            {
                $error_set[] = ERROR_house;
            }
        }        
        if ($_POST['firstName'] == '')
        { 
            $error_set[] = ERROR_first_name;
        }
        else
        {
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*[^s]$/', $_POST['firstName']))
            {
                $error_set[] = ERROR_first_name_wrong_format;
            }
            else
            {
                $result['first_name'] = $_POST['firstName'];
            }
        }
        if ($_POST['lastName'] == '') 
        {
            $error_set[] = ERROR_last_name;
        }
        else
        {
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*[^s]$/', $_POST['lastName']))
            {
                $error_set[] = ERROR_last_name_wrong_format;
            }
            else
            {
                $result['last_name'] = $_POST['lastName'];
            }
        }
        if ($_POST['Email'] == '')
        {
            $error_set[] = ERROR_email_missed;
        }
        else
        {
            if (!preg_match('/^[a-zA-Z][-a-zA-Z0-9\.]*@[a-zA-Z][-a-zA-Z]*\.[a-zA-Z]+[^s]$/', $_POST['Email']))
            {
                $error_set[] = ERROR_email_wrong_format;
            }
            else
            {
                $result['email'] = $_POST['Email'];
            }
        }
        if ($_POST['dateArrive'] == '') 
        {
            $error_set[] = ERROR_date_arrive;
        }
        else
        {
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['dateArrive']))
            {
                $error_set[] = ERROR_date_arrive_wrong_format;
            }
            else
            {
                if (check_order_date($_POST['dateArrive']))
                {
                    $result['date_arrive'] = $_POST['dateArrive'];
                }
                else
                {
                    $error_set[] = ERROR_date_arrive_occupied;
                }
            }
        }
        if ($_POST['dateDeparture'] == '') 
        {
            $error_set[] = ERROR_date_departure;
        }
        else
        {
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['dateDeparture']))
            {
                $error_set[] = ERROR_date_departure_wrong_format;
            }
            else
            {
                if (check_order_date($_POST['dateDeparture']))
                {
                    $result['date_departure'] = $_POST['dateDeparture'];
                }
                else
                {
                    $error_set[] = ERROR_date_departure_occupied;
                }
            }
        }
        if ($_POST['comment'] == '')
        {
            $error_set[] = ERROR_comment;
        }
        else
        {
            $result['comment'] = preg_replace('/[*_]*/','',mysql_escape_string($_POST['comment']));
        }
		
		if ($_POST["recaptcha_response_field"])
		{
			$resp = recaptcha_check_answer (PRIVATEKEY,
											$_SERVER["REMOTE_ADDR"],
											$_POST["recaptcha_challenge_field"],
											$_POST["recaptcha_response_field"]);
			
						
			if (!($resp->is_valid))
			{
				# set the error code so that we can display it
				$error = $resp->error;
				$error_set[] = ERROR_captcha;
				
			}
		}
		else
		{
			$error_set[] = ERROR_captcha_missed;
		}
		
		$error_captcha = '<script type="text/javascript">Recaptcha.create("'.PUBLICKEY.'","captcha", {theme: "red", callback: Recaptcha.focus_response_field});</script>';
		
        return $result;
    }
    
    $args = form_validate();
    
    if (!count($error_set))
    {   
        mysql_query('INSERT INTO orders VAlUES ( \'\', ' . 
                                $args['houseId'] . ', \'' .
                                $args['first_name'] . '\', \'' .
                                $args['last_name'] . '\', \'' .
                                $args['email'] . '\', \'' .
                                $args['date_arrive'] . '\', \'' .
                                $args['date_departure'] . '\', ' .
                                'CURRENT_TIMESTAMP(), ' .
                                '0, \'' .
                                $args['comment'] . '\');');
        $args['orderId'] = (int)mysql_insert_id($connection);
        $message_set[] = MESSAGE_order_number . $args['orderId'] . MESSAGE_order_add;
        mail_user_eng($args);
        mail_admin($args);
    }

    result_to_xml ($error_set, $message_set, $error_captcha);
?>
