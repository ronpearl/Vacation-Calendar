<?php

	/*
	*	Return array of dates that are between a set start and end date.
	*/
	function getDatesFromRange($start, $end){
		$dates = array($start);
		while(end($dates) < $end){
			$dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
		}
		return $dates;
	}
	
	
	/*
	*	Set Holiday Dates.
	*	Checks a specific day to see if it lands on a pre-defined holiday.
	*	Returns boolean and the name of the holiday.
	*/
	function checkHolidays($dateToCheck, $holidayArray)
	{
		$resultName = "not_a_holiday";
		
		foreach ($holidayArray as $key => $val)
		{
			if ($val == $dateToCheck)
			{
				$resultName = $key;
			}
		}
		unset($key);
		unset($val);
		
		return $resultName;
	}
?>