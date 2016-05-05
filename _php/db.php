<?php

### DB Library for dolgostroyu.net
### by Anton. Y. Reuchenko
### ==============================
### v.1.0.2 (05.09.2012) - replace all \" in value for &quot;
### v.1.0.1 (04.09.2012) - add default trim for all incomming params
### v.1.0.0 - start of project development

$oConnection = null;
function executeSQLQuery($_sQuery, $_aData = array(), $_fetch = true) {
	global $oConnection;
	
	$sQuery = "";
	
	/* */
	// $oConnection = null;
	$aResult = array();
	
	if($oConnection == null) {
		$oConnection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);// this is for procedures, null, 196608);
		// if(!$oConnection)
		// {
		// 	   die("Can't connect to mysql server");
		// 	   return FALSE;
		// }
		// /* проверяем соединение */
		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}

		// Select database
		if(!mysqli_select_db($oConnection, DB_DATABASE_NAME))
		{
		    die("Can't select database");
		    return FALSE;
		}

		mysqli_set_charset($oConnection, 'utf8');
		/* */
	}

	// create request
	if(is_string($_sQuery) && is_array($_aData) && count($_aData)) {
		for($i = 0; $i < count($_aData); $i++) {
			$pattern = "/\\$" . ($i + 1) . "([^\d]|$)/";
			
			if(!is_null($_aData[$i])) {
				$_aData[$i] = trim($_aData[$i]);
				$_aData[$i] = preg_replace("/\"/m", "&quot;", $_aData[$i]);
				$_aData[$i] = preg_replace("/[\r\n]/m", "", $_aData[$i]);
				$_aData[$i] = "'" . mysqli_real_escape_string($oConnection, $_aData[$i]) . "'";
			} else {
				$_aData[$i] = 'NULL';
			}

			$_sQuery = preg_replace($pattern, $_aData[$i] . "\\1", $_sQuery, 1);
		}
		
		$sQuery = $_sQuery;
	} else {
		$sQuery = $_sQuery;
	}

	$fetch = ($_fetch == false) ? false : true;
	
	// echo $sQuery;echo '<br />';
	// var_dump($_aData);
	// return false;
	// exit(0);
	
$tSQLSelectStart = microtime(true);
	if($oResult = mysqli_query($oConnection, $sQuery)) {
		if($fetch === true && $oResult !== true) {
			while($aData = getAssocArray($oResult)) {
				array_push($aResult, $aData);
			}
			mysqli_free_result($oResult);
			mysqli_next_result($oConnection);
		} else {
			$aResult = mysqli_insert_id($oConnection);
		}
	} else {
		// echo mysqli_error($oConnection);
	}

	// mysqli_close($oConnection);
if(defined("GLOBAL_DEBUG_MODE") && GLOBAL_DEBUG_MODE === true && array_key_exists("showwastedtime", $_GET) && !array_key_exists("mode", $_GET)) {
	echo "DB query: $sQuery spent: " . (round((microtime(true) - $tSQLSelectStart) * 1000)) . " miliseconds&hellip;<br />";
}
	// echo "<pre>";
	// var_dump($aResult);
	// echo "</pre>";

	return $aResult;
}

function callSQLProcedure($_sQuery, $_fetch = true) {
	global $oConnection; // = null;
	$aResult = array();
	
	$oConnection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);// this is for procedures, null, 196608);
	if(!$oConnection)
	{
	    die("Can't connect to mysql server");
	    return FALSE;
	}
	
	// Select database
	if(!@mysqli_select_db(DB_DATABASE_NAME, $oConnection))
	{
	    die("Can't select database");
	    return FALSE;
	}
	
	mysqli_set_charset('utf8', $oConnection);

	$fetch = ($_fetch == false) ? false : true;

	if($oResult = @mysqli_query($_sQuery, $oConnection)) {
		if($fetch === true) {
			// while($aData = getAssocArray($oResult)) {
			while($aData = getAssocArray($oResult)) {
				array_push($aResult, $aData);
			}
			mysql_free_result($oResult);
		} else {
			$aResult = mysql_insert_id($oConnection);
		}
	}

	mysqli_close($oConnection);
	
	return $aResult;
}

function callQuery($_sQuery, $_fetch = true) {
	return callSQLProcedure($_sQuery, $_fetch);
}

function getAssocArray($_oResult) {
	$aReturn = array();
	$aData = mysqli_fetch_assoc($_oResult);
	if($aData != null) {
		while(list($key, $value) = each($aData)) {
			$aReturn[$key] = $value;
		}
	}
	return $aReturn;
}

// function getAssocArray($_oResult) {
// 	$aReturn = array();
// 	$aData = mysql_fetch_assoc($_oResult);
// 	if($aData != null) {
// 		while(list($key, $value) = each($aData)) {
// 			$aReturn[$key] = is_null($value) ? $value : htmlspecialchars($value, ENT_QUOTES);
// 		}
// 	}
// 	return $aReturn;
// }

function exportDataForTable( $_sTableName = null ) {
	$sRetuls = "";
	if($_sTableName != null && is_string($_sTableName)) {
		$result = executeSQLQuery("SELECT * FROM information_schema.tables WHERE table_schema = $1 AND table_name = $2;", array(
			DB_DATABASE_NAME,
			$_sTableName
		));
		
		if(count($result) == 1) {
			$sResult  = "LOCK TABLES `" . $_sTableName . "` WRITE;\n";
			$sResult .= "/*!40000 ALTER TABLE `" . $_sTableName . "` DISABLE KEYS */;\n";
			
			// table exist, let's start to get all data from it
			$result = executeSQLQuery("SELECT * FROM `" .$_sTableName. "`;", array(
			));
			$aItems = array();
			if(count($result)) {
				for($i = 0; $i < count($result); $i++) {
					$aItem = array();
					while(list($key, $val) = each($result[$i])) {
						// array_push($aItem, is_null($val) ? "NULL" : (($key=="hash" || $key=="uid") ? "UNHEX('" . join("", unpack("H32", $val)) . "')" : "'" . preg_replace("/&#039;/m", "\\'", htmlspecialchars_decode($val, ENT_XHTML | ENT_NOQUOTES)) . "'"));
						array_push($aItem, is_null($val) ? "NULL" : (($key=="hash" || $key=="uid") ? "UNHEX('" . join("", unpack("H32", $val)) . "')" : "'" . preg_replace("/&#039;/m", "\\'", htmlspecialchars_decode($val, ENT_NOQUOTES)) . "'"));
					}
					array_push($aItems, "(" . join(",", $aItem) . ")");
					
					if(count($aItems) == 100) {
						$sResult .= "INSERT INTO `" . $_sTableName . "` VALUES " . join($aItems, ",") . ";\n";
						$aItems = array();
					}
				}
			}
			
			if(count($aItems)) {
				$sResult .= "INSERT INTO `" . $_sTableName . "` VALUES " . join($aItems, ",") . ";\n";
			}
			$sResult .= "/*!40000 ALTER TABLE `" . $_sTableName . "` ENABLE KEYS */;\n";
			$sResult .= "UNLOCK TABLES;\n\n";
		}
		
		
		
		
	}
	
	return $sResult;
}

?>