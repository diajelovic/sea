<?php
   	header("Content-Type: text/xml; charset=utf-8");
    switch($_POST['lang']) {
		case 'es': require_once('../config/eslang.php'); break;
		default: require_once('../config/enlang.php'); break;
	}
	require_once('../config/config.php');
    $house_id = $_POST['houseId'];
    $year = date("Y");
    $dayNames = Array (
            'Monday' => CALENDAR_MONDAY,
            'Tuesday' => CALENDAR_TUESDAY,
            'Wednesday' => CALENDAR_WEDNESDAY,
            'Thursday' => CALENDAR_THURSDAY,
            'Friday' => CALENDAR_FRIDAY,
            'Saturday' => CALENDAR_SATURDAY,
            'Sunday' => CALENDAR_SUNDAY);            
    $dayNamesShort = Array (
            'Monday' => CALENDAR_MONDAY_SHORT,
            'Tuesday' => CALENDAR_TUESDAY_SHORT,
            'Wednesday' => CALENDAR_WEDNESDAY_SHORT,
            'Thursday' => CALENDAR_THURSDAY_SHORT,
            'Friday' => CALENDAR_FRIDAY_SHORT,
            'Saturday' => CALENDAR_SATURDAY_SHORT,
            'Sunday' => CALENDAR_SUNDAY_SHORT);
    $monthNames = Array (
            'January' => CALENDAR_January,
            'February' => CALENDAR_February,
            'March' => CALENDAR_March,
            'April' => CALENDAR_April,
            'May' => CALENDAR_May,
            'June' => CALENDAR_June,
            'July' => CALENDAR_July,
            'August' => CALENDAR_August,
            'September' => CALENDAR_September,
            'October' => CALENDAR_October,
            'November' => CALENDAR_November,
            'December' => CALENDAR_December
            );
    
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
     * Функция преобразования календаря в xml
     * Автор Андрей Борха
     * */
    function result_to_xml($data){
    	global $monthNames;
    	
    	$result =  '<?xml version="1.0" encoding="UTF-8"?>';
        $result .= '<result>';
        
        foreach ($monthNames as $key => $value) {
            $result .= '<month monthname="' . $value . '">';
        	for ($i = 0; $i < count($data[$key]) ; $i++)
        	{
                $result .= '<week>';
        		for ($j = 0; $j < count($data[$key][$i]) ; $j++)
                {
                    $result .= '<date type="' . $data[$key][$i][$j]['type'] . '" weekend="' . $data[$key][$i][$j]['weekend'] . '" dayname="' . $data[$key][$i][$j]['dayName'] . '" dayshortname="' . $data[$key][$i][$j]['dayShortName'] . '">';
                    $result .= $data[$key][$i][$j]['date'] . '</date>';             	
                }
                $result .= '</week>';
        	}
        	$result .= '</month>';
        }
        
        $result .= '</result>';
        
        echo $result;
    }
    
    /*
     * Функция получения занятых дат
     * Автор Андрей Борха
     * */
    function get_occupied_dates ($houseId){
        $result = Array();
        
        $q = mysql_query('SELECT begin_date as dateBegin,
                                 end_date as dateEnd
	                      FROM orders
	                      WHERE house_id = ' . $houseId . ';');

	    while ($row = mysql_fetch_assoc($q))
	    {
	        $result[] = $row;
	    }
        
        return $result;
    }
    
    /*
     * Функция определения занят ли день
     * Автор Андрей Борха
     * */
    function get_day_type($time){
    	global $occupiedDates;
    	
    	$result = CALENDAR_OPEN_TYPE;
    	
    	foreach ($occupiedDates as $value)
    	{
    	   	if (date("Y-m-d", $time) >= $value['dateBegin'] && date("Y-m-d", $time) <=  $value['dateEnd'])
    	   	{
    	   	   $result = CALENDAR_CLOSED_TYPE;
    	   	}
    	}
    	
    	if (date("Y-m-d", $time) <= date("Y-m-d"))
    	{
    		$result = CALENDAR_CLOSED_TYPE;
    	}
    	
    	return $result;
    }
    
    /*
     * Функция создания  календаря
     * Автор Андрей Борха
     * */
    function get_calendar ($year){
    	global $dayNames;
    	global $monthNames;
    	global $dayNamesShort;
    	
    	$result = Array();
    	$i = 0;
    	
    	foreach ($monthNames as $monthkey => $monthvalue) {
    		$month = Array();
    		$days = date('j', mktime(0,0,0, $i+1, 0, $year));
    		$j = 1;
    		$beginCount = false;
    		$week = Array (); 
    		
    	    foreach ($dayNames as $key => $value) {
                $weekend = ($k > 5) ? 'true' : 'false';
                
                if ($key == date('l', mktime(0,0,0, $i, $j, $year)))
                {
                	$beginCount = true;
                }
                
                if ($beginCount) {
	    	    	$week[] = Array(   
	                    'date' => $j,
	                    'type' => get_day_type(mktime(0,0,0, $i, $j, $year)),
	    	    	    'weekend' => $weekend,
	                    'dayName' => $dayNames[$key],
	                    'dayShortName' => $dayNamesShort[$key],                  
	                );
	                
	                $j++;
                }
                else
                {
                	$week[] = Array(   
                        'date' => 0,
                        'type' => CALENDAR_CLOSED_TYPE,
                	    'weekend' => $weekend,
                        'dayName' => $dayNames[$key],
                        'dayShortName' => $dayNamesShort[$key],                  
                    );
                }
            }
            
            $month[] = $week;
            
    		while ($j <= $days)
    		{
    			$week = Array ();
    			
    			for ($k=1; $k<=7; $k++){
	    			$weekend = ($k > 5) ? 'true' : 'false';
	    			
	    			if ($j > $days)
	    			{
	    				$week[] = Array(   
                            'date' => 0,
                            'type' => CALENDAR_CLOSED_TYPE,
                            'weekend' => $weekend,
                            'dayName' => $dayNames[date('l', mktime(0,0,0, $i, $j, $year))],
                            'dayShortName' => $dayNamesShort[date('l', mktime(0,0,0, $i, $j, $year))],                  
                        );
	    			}
	    			else
	    			{
		    			$week[] = Array(   
		                    'date' => $j,
		                    'type' => get_day_type(mktime(0,0,0, $i, $j, $year)),
		    			    'weekend' => $weekend,
		                    'dayName' => $dayNames[date('l', mktime(0,0,0, $i, $j, $year))],
		                    'dayShortName' => $dayNamesShort[date('l', mktime(0,0,0, $i, $j, $year))],                  
		                );
	    			}
	                
	                $j++;
                }
                
                $month[] = $week;
                
    		}
    		
    		$result[$monthkey] = $month;
    		$i++; 
    	}
    	
    	return $result;
    }
    
    $occupiedDates = get_occupied_dates($house_id);
    result_to_xml(get_calendar($year));
    
?>
