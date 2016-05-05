<?php

function br2nl ( $string ){
    return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

function rowsCount ( $string ){
    return preg_match('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

function hasRule($rule){
	return array_key_exists(SESSION_USER_PRIVILEGES, $_SESSION) && array_key_exists($rule, $_SESSION[SESSION_USER_PRIVILEGES]);
}

function getParam( $_name, $_defval = null) {
	return (
		($_name != null && is_string($_name))
			? (
				(isset($_GET[$_name])/* && is_string($_GET[$_name])*/) 
					? (
						(/*strlen(*/$_GET[$_name]/*)*/) 
						? $_GET[$_name] : $_defval
						// $_GET[$_name]
					  ) 
					: (
						(isset($_POST[$_name])/* && is_string($_POST[$_name])*/) 
							? (
								(/*strlen(*/$_POST[$_name]/*)*/) 
								? $_POST[$_name] : $_defval
								// $_POST[$_name]
					  		)  
					  		: $_defval
					 )
				) 
			: 
			$_defval
	);
}

function object_to_array($data)
{
	if (is_array($data) || is_object($data))
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			$result[$key] = object_to_array($value);
		}
		return $result;
	}
	return $data;
}


function getArrayParam($_name, $_array){
	$result= '-1';
	for($i=0;$i<count($_array);$i++){
		if ($_array[$i]['id'] == $_name){
			return $_array[$i]['value'];
		}
	}
	return $result;
}

function getBitErrors($_result, $_aMessages = array()) {
	$mask = 1;
	$sMessage = "";
	$sErroressage = "";
	if($_result < 0) {$_result = -1 * $_result;}
	while (($sErrorMessage = array_shift($_aMessages))) {
		if($_result & $mask) {
			$sMessage .= ((strlen($sMessage)) ? ", " : "") . $sErrorMessage;
		}
		// make left bit shift 
		$mask = $mask << 1;
	}
	return $sMessage;
}

function login() {
	$isLoginned = false;
	$username = preg_replace("/^\+/", "",getParam(LOGIN_USERNAME, ""));
	$userpassword = getParam(LOGIN_PASSWORD, "");
	if(strlen($username) && strlen($userpassword))
	{
		// check username
		$result = executeSQLQuery("SELECT fnUser_login($1, $2) as 'result';", array(
			$username,
			$userpassword
		));
		
		if($result[0]["result"] != "" &&  intval($result[0]["result"]) > 0)
		{
			// store usernme in session
			$_SESSION[SESSION_USERID] = $result[0]["result"];
			
			$result = executeSQLQuery("CALL pcUser_getPrivileges($1);", array(
				$_SESSION[SESSION_USERID]
			));
			
			// var_dump($result);
			if(count($result)) {
				if(!array_key_exists(SESSION_USER_PRIVILEGES, $_SESSION)) {
					$_SESSION[SESSION_USER_PRIVILEGES] = array();
				}
				
				for($i = 0; $i < count($result); $i++) {
					$_SESSION[SESSION_USER_PRIVILEGES][$result[$i]["name"]] = "1";
				}
			}
			
			$isLoginned = 0;
		}else{
			if(isset($_SESSION[SESSION_ATTEMPT_COUNTER]))
			{
				$_SESSION[SESSION_ATTEMPT_COUNTER]++;
			}
			else
			{
				$_SESSION[SESSION_ATTEMPT_COUNTER] = 0;
			}
			$isLoginned = $result[0]["result"];
		}
		
		// $sData = "{\"result\":\"" . $nResult . "\"}";
		
		// store failed login attempt
		/*if(isset($_SESSION))
		{
			if($nResult == 0) {
				if(isset($_SESSION[SESSION_ATTEMPT_COUNTER]))
				{
					$_SESSION[SESSION_ATTEMPT_COUNTER]++;
				}
				else
				{
					$_SESSION[SESSION_ATTEMPT_COUNTER] = 0;
				}
			} else {
				$_SESSION[SESSION_ATTEMPT_COUNTER] = null;
			}
		}*/
		
		// header("HTTP/1.1 301 Moved Permanently");
		// if($nResult == 1) {
		// 	header("Location: " . preg_replace("/\?fail/", "", $GLOBALS["HTTP_SERVER_VARS"]["REQUEST_URI"]));
		// } else {
		// 	header("Location: " . preg_replace("/\?fail/", "", $GLOBALS["HTTP_SERVER_VARS"]["REQUEST_URI"]) . "?fail");
		// }
	}
	
	return $isLoginned;
}

function check_email_address($email) {
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
	  return false;
	}
	
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
    	}
  	}

	// Check if domain is IP. If not, 
	// it should be valid domain name
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}

		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
				return false;
			}
		}
	}

	return true;
}

function send_mime_mail($name_from, // имя отправителя
                        $email_from, // email отправителя
                        $name_to, // имя получателя
                        $email_to, // email получателя
                        $data_charset, // кодировка переданных данных
                        $send_charset, // кодировка письма
                        $subject, // тема письма
                        $body // текст письма
                        ) {
	$aToEmails = split(",", $email_to);
	$aToNames = split(",", $name_to);
	for($i = 0; $i < count($aToEmails); $i++) {
		$aToEmails[$i] = mime_header_encode((array_key_exists($i, $aToNames) ? $aToNames[$i] : $aToNames[0]), $data_charset, $send_charset)
		                 . ' <' . $aToEmails[$i] . '>';
	}
	$to = join(",", $aToEmails);
	
	$from = mime_header_encode($name_from, $data_charset, $send_charset)
	                 . ' <' . $email_from . '>';
	
	$subject = mime_header_encode($subject, $data_charset, $send_charset);
	if($data_charset != $send_charset) {
		$body = iconv($data_charset, $send_charset, $body);
	}
	$headers = "From: $from\r\n";
	$headers .= "Content-type: text/html; charset=$send_charset\r\n";
	return mail($to, $subject, $body, $headers);
}

function mime_header_encode($str, $data_charset, $send_charset) {
  if($data_charset != $send_charset) {
    $str = iconv($data_charset, $send_charset, $str);
  }
  return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
}

// function pageNotFound() { 
// 	//header("HTTP/1.1 403 Forbidden");
// 	//exit(0);
// 	header('HTTP/1.x 404 not found');
// 	return array(
// 		"pageNotFound" => true,
// 		"pageClass" => "",
// 		"title" => "Страница не найдена",
// 		"controls" =>  array(
// 			array("template" => "pagenotfound.tpl")
// 		)
// 	);
// 	//exit(0);
// }

function generateKey($_lenght = 128, $_symbols = "0123456789abcdefghijklmnopqrstvuwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
	//$symbols = "0123456789abcdefghijklmnopqrstvuwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$key = "";
	for($x = 0; $x < $_lenght; $x++) {
		$key .= $_symbols[round(rand(0, strlen($_symbols)-1))];
	}
	return $key;
}

?>