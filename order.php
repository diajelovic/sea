<?php
	ini_set('display_errors', true);
	ini_set('error_reporting', E_ALL);
	
	$houses = array(
		'1' => 'Daniela',
		'2' => 'Berlin'
	);

    require_once('_php/config.php');
    require_once('_php/common.php');
    require_once('_php/db.php');
    require_once('_php/enlang.php');
    require_once('_php/libs/PHPMailer/PHPMailerAutoload.php');
	require_once('_php/libs/recaptcha/recaptchalib.php');

	$result = array(
		'result' => 0,
		'errors' => array(),
		'messages' => array(MESSAGE_order_number . ' ' . MESSAGE_order_add)
	);

	$houseId = getParam('houseId');
	$firstName = getParam('firstName');
	$lastName = getParam('lastName');
	$email = getParam('Email');
	$dateArrive = getParam('dateArrive');
	$dateDeparture = getParam('dateDeparture');
	$comment = getParam('comment');
	$captcha = getParam('recaptcha_response_field');
	$captchaC = getParam('recaptcha_challenge_field');

	if (!$houseId){
		$result['errors'][] = ERROR_house;
	}
	if (!$firstName){
		$result['errors'][] = ERROR_first_name;
	}
	if (!$lastName){
		$result['errors'][] = ERROR_last_name;
	}
	if (!$email){
		$result['errors'][] = ERROR_email_missed;
	}
	if (!$dateArrive){
		$result['errors'][] = ERROR_date_arrive;
	}
	if (!$dateDeparture){
		$result['errors'][] = ERROR_date_departure;
	}

	if ($captcha){
		$resp = recaptcha_check_answer (PRIVATEKEY,$_SERVER["REMOTE_ADDR"],$captchaC,$captcha);

		if (!($resp->is_valid)){
			# set the error code so that we can display it
			$error = $resp->error;
			$result['errors'][] = ERROR_captcha;
		}
	}else{
		$result['errors'][] = ERROR_captcha_missed;
	}
	$result['captcha'] = '<script type="text/javascript">Recaptcha.create("'.PUBLICKEY.'","captcha", {theme: "red", callback: Recaptcha.focus_response_field});</script>';

	if (count($result['errors'])){
		$result['result'] = 0;
	}else{
		// $result['result'] = 1;
		$message = 'Hello ' . $firstName . ' ' . $lastName . "!<br/><br/>";
        $message .= 'Your order was added.<br/>';
        $message .= 'House - ' . $houses[$houseId] . '<br/>';
        $message .= 'Date arrive - ' . $dateArrive . "<br/>";
        $message .= 'Date departure - ' . $dateDeparture . ";<br/>";
        $message .= "Wait till Admin submit it.<br/><br/>";
        $message .= 'Best regards SeaAndhouse.com';

		$result = mailer(array(
			'from' => ADMIN_EMAIL,
			'pass' => ADMIN_EMAIL_PASSWORD,
			'fromName' => 'Sea And House',
			'to' => array('urza@mailinator.com'),
			'subject' => 'Order',
			'message' => $message
		));
		// if ($result['result'] > 0){
		// 	$result = mailer(array(
		// 		'from' => ADMIN_EMAIL,
		// 		'pass' => ADMIN_EMAIL_PASSWORD,
		// 		'fromName' => 'Sea And House',
		// 		'to' => array($email),
		// 		'subject' => 'Order',
		// 		'message' => 
		// 	));
		// }
	}

	function mailer($data){
		$result = array(
			'result' => 0,
			'errors' => array(),
			'messages' => array()
		);
		$mail = new PHPMailer;
		$mail->CharSet = "UTF-8";
		// $message = '<html>';
		// $message .= '<head>';
		// $message .= '<title>'. $data['formName'] . '</title>';
		// $message .= '</head>';
		// $message .= '<body>';
		$message = $data['message'];
		// $message .= '</body>';
		// $message .= '</html>';

		// $mail->isSMTP();                                      // Set mailer to use SMTP
		// $mail->Host = 'mail.selectmanagement.ru;';  		  // Specify main and backup SMTP servers
		// $mail->SMTPAuth = true;                               // Enable SMTP authentication
		// $mail->Username = 'robot@selectmanagement.ru';        // SMTP username
		// $mail->Password = 'robot';                            // SMTP password
		// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		// $mail->Port = 25;                                    // TCP port to connect to
		$mail->SMTPDebug = 1;
		$mail->isSMTP();  		                                    // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';  		  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		// $mail->Username = 'mail@select.agency';        // SMTP username
		$mail->Username = $data['from'];        // SMTP username
		// $mail->Password = 'FRbnc8dW';                            // SMTP password
		$mail->Password = $data['pass'];                            // SMTP password
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to

		// $mail->DKIM_domain = 'example.com';
		// $mail->DKIM_private = '/path/to/my/private.key';
		// $mail->DKIM_selector = 'phpmailer';
		// $mail->DKIM_passphrase = '';
		// $mail->DKIM_identity = $mail->From;

		// $mail->From = $data['from'];
		if (isset($data['from']) && $data['from']){
			$mail->From = $data['from'];
			// $mail->From = 'mail@select.agency';
		}
		if (isset($data['fromName']) && $data['fromName']){
			$mail->FromName = $data['fromName'];
		}
		foreach ($data['to'] as $mailTo) {
			$mail->addAddress($mailTo);     // Add a recipient
		}
		// foreach ($data['files'] as $key => $value) {
		// 	$mail->addAttachment($value, $key);    // Optional name
		// }
		// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $data['subject'];
		$mail->Body    = $message;

		if(!$mail->send()) {
		    $result['errors'][] = 'Message could not be sent';
			$result['errors'][] = 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			$result['result'] = 1;
			if (isset($data['messageSend']) && $data['messageSend']){
				$result['messages'][] = $data['messageSend'];
			}else{
			    $result['messages'][] = 'Message has been sent';
			}
		}

		return $result;
	}

	// function mail_user_eng($args)
 //    {
 //    	
 //        mail($args['email'], 'order ¹' . $args['orderId'], $message);
 //    }
    
 //    function mail_admin($args)
 //    {
 //        $message = 'Order ¹' . $args['orderId'] . " was added.\n\n";
 //        $message .= 'House - ' . get_house_name($args['houseId']) . ";\n";
 //        $message .= 'Date arrive - ' . $args['date_arrive'] . ";\n";
 //        $message .= 'Date departure - ' . $args['date_departure'] . ";\n";
 //        $message .= 'Comment - ' . $args['comment'] . ";\n\n";
 //        $message .= 'Commit Link: <a href="'.SITE_NAME.'/mailconfirm.php?md=' . md5($args['orderId']) . '">' . md5($args['orderId']) . '</a>';
        
 //        mail(ADMIN_EMAIL, 'order ¹' . $args['orderId'], $message);
 //    }

	echo json_encode($result);
	exit(0);
?>
