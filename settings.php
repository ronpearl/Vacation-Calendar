<?php
	/*
	*	Holds generic settings that can be used across the entire site.
	*/
	
	$siteRoot = 'http://yourdomain.com';
	
	// Admin Email - Used for vacation submissions
	$adminEmail = 'adminemail';
	
	// noreply email names
	$noReplyEmail = 'noreply@domain.com';
	
	// Array of holidays that will be checked on the calendar
	// Names will display on the specific date.
	$holidayArray = array(
		"New Year's Day" 			=> "2015-01-01",
		"Martin Luther King Day" 	=> "2015-01-19",
		"President's Day" 			=> "2015-02-16",
		"Good Friday" 				=> "2015-04-03",
		"Memorial Day" 				=> "2015-05-25",
		"Independence Day" 			=> "2015-07-03",
		"Labor Day" 				=> "2015-09-07",
		"Thanksgiving Day" 			=> "2015-11-26",
		"Day After Thanksgiving" 	=> "2015-11-27",
		"Christmas Eve" 			=> "2015-12-24",
		"Christmas Day" 			=> "2015-12-25",
		"New Year's Eve (Half Day)" => "2015-12-31"
	);
	
	// Turn the oncall system on/off
	$onCallService = true;
?>