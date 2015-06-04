<?php
	session_start();
	
	$userID = $isAdmin = "";
	
	if (!isset($_SESSION["login_user"]) || !isset($_SESSION["login_id"]))
	{
		// Set view mode based upon log-in status
		$loggedIn = false;
	} else {
		$loggedIn = true;
		$userID = $_SESSION["login_id"];
		
		$isAdmin = false;
		if ($_SESSION["admin"] == 1) { $isAdmin = true; }
	}
	
	require('settings.php');
	include_once('includes/baseConnection.php');
	include_once('includes/dateFunctions.php');
	include_once('includes/buildModal.php');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Vacation Calendar - GCU Web Design</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/calendar.css" rel="stylesheet">
    <link href="css/bootstrap-colorpicker.css" rel="stylesheet">
    
    <link type="text/css" rel="stylesheet" href="css/bootstrap-datetimepicker.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  
  	<!-- Screen Overlay when logging in -->
    <div class="working"><i class="fa fa-spinner fa-spin"></i></div>
  
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $siteRoot.'wdvacations'; ?>">Web Design Vacation Calendar</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <?php include('includes/navLogin.php'); ?>
                </ul>
            </div><!--/.nav-collapse -->
      	</div>
    </nav>
    
    <?php
		date_default_timezone_set('America/Phoenix');
	
		$today = getdate();
		
		$monthValue = "";
		
		if(isset($_GET['mon'])){
			$monthValue = $_GET['mon'];
			
		   if(isset($_GET['year'])){
			  $start = mktime(0,0,0,$_GET['mon'],1,$_GET['year']);
		   }
		   else{
			  $start = mktime(0,0,0,$_GET['mon'],1,$today['year']);
		   }
		}
		else{
		   $start = mktime(0,0,0,$today['mon'],1,$today['year']);
		}
		
		$first = getdate($start);
		$end = mktime(0,0,0,$first['mon']+1,0,$first['year']);
		$last = getdate($end);
		
		// Now lets take the next/previous links and work with them to allow for incrementing the calendar months.
		// check if there are values submitted, if so create the info.  Otherwise, create the info based on todays date
		if ($monthValue == 12)
		{
			$next_month = 1;
			$yearInNextLink = $_GET['year'] + 1;
			
			$last_month = 11;
			$yearInPrevLink = $_GET['year'];
		}
		elseif ($monthValue== 1)
		{
			$next_month = 2;
			$yearInNextLink = $_GET['year'];
			
			$last_month = 12;
			$yearInPrevLink = $_GET['year'] - 1;
		}
		else
		{
			$next_month = $first['mon'] + 1;
			$yearInNextLink = $first['year'];
			
			$last_month = $first['mon'] - 1;
			$yearInPrevLink = $first['year'];
		}
	?>
    
    <section>
    	<div class="container-fluid calendar">
        	<div class="row no-gutter">
                
            	<div class="row">
                    <div class="monheader">
                        <div class="col-md-4">
                            <a href="index.php?mon=<? echo $last_month; ?>&year=<? echo $yearInPrevLink; ?>"><i class="fa fa-arrow-left"></i></a>
                        </div>
                        <div class="col-md-4">
                            <?php echo $first['month'] . ' - ' . $first['year']; ?>
                        </div>
                        <div class="col-md-4">
                            <a href="index.php?mon=<? echo $next_month; ?>&year=<? echo $yearInNextLink; ?>"><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-1 dayheader">Sun</div>
                    <div class="col-md-1 dayheader">Mon</div>
                    <div class="col-md-1 dayheader">Tue</div>
                    <div class="col-md-1 dayheader">Wed</div>
                    <div class="col-md-1 dayheader">Thu</div>
                    <div class="col-md-1 dayheader">Fri</div>
                    <div class="col-md-1 dayheader">Sat</div>
                </div>
                
                <div class="row">
					<?php
                        $style = $namesToDisplay = $onCallToDisplay = "";
						
						//$todayDateToFormat = new DateTime($today['year']."-".$today['mon']."-".$today['mday']);
						//$todayDayDate = date_format($todayDateToFormat, 'Y-m-d');
						
						$firstDayDateToFormat = new DateTime($first['year']."-".$first['mon']."-".$first['mday']);
						$firstDayDate = date_format($firstDayDateToFormat, 'Y-m-d');
						
						$lastDayDateToFormat = new DateTime($last['year']."-".$last['mon']."-".$last['mday']);
						$lastDayDate = date_format($lastDayDateToFormat, 'Y-m-d');
						
						// Get all instances of vacations during this month
						$thisMonthResults = "";
						try {
							$db = new baseConnection();
							$conn = $db->getConn();
							$query = $conn->prepare("SELECT * FROM vacations_requests WHERE startDate BETWEEN '$firstDayDate' AND '$lastDayDate' OR endDate BETWEEN '$firstDayDate' AND '$lastDayDate' ORDER BY user");
							$query->execute();
							$thisMonthResults = $query->fetchAll();
							
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
						
                        for($i = 0; $i < $first['wday']; $i++){
                           echo '  <div class="col-md-1 inactive"></div>' . "\n";
                        }
                        
                        for($i = 1; $i <= $last['mday']; $i++){
							// Set the date for each iteration of the loop
							// This will allow us to manipulate certain days of the month
							$thisLoopedDay_literal = $first['year']."-".sprintf("%02d", $first['mon'])."-".sprintf("%02d", $i);
							
							$thisLoopedDay = new DateTime($thisLoopedDay_literal);
							$dayOfWeek =  date("w", $thisLoopedDay->getTimestamp());
							$weekOfTheYear = date("W", $thisLoopedDay->getTimestamp());
							
							// Check to see if this day is a holiday
							$holidayName = checkHolidays($thisLoopedDay_literal, $holidayArray);
							$holidayDisplay = "";
							
                            if($i == $today['mday'] && $first['mon'] == $today['mon'] && $first['year'] == $today['year']){
                              	$style = 'today';
                            } elseif ($dayOfWeek == 0 || $dayOfWeek == 6) {
								// If it's a weekend, use weekend colors
								$style = 'weekend';
							} else if ($holidayName != "not_a_holiday") {
								$style = 'holiday';
								$holidayDisplay = '<div class="shownHoliday">'.$holidayName.'</div>';
							} else {
                              	$style = 'day';
                            }
							
							// If oncall service is turned on, add these items
							if ($onCallService)
							{
								// If it's a Mond or Fri... this is where we will display the oncall person
								if ($dayOfWeek == 1 || $dayOfWeek == 5)
								{
									try {
										$query3 = $conn->prepare("SELECT user FROM vacations_oncall WHERE week_number = '$weekOfTheYear'");
										$query3->execute();
										$oncallPerson = $query3->fetch();
										
										$query3 = $conn->prepare("SELECT first, last FROM vacations_users WHERE uid = '".$oncallPerson['user']."'");
										$query3->execute();
										$oncallPersonName = $query3->fetch();
									} catch(PDOException $e) {
										echo $e->getMessage();
									}
								
									$onCallToDisplay = '
										<div class="oncall-box" data-tooltip="tooltip" title="ON CALL: '.$oncallPersonName['first'].' '.$oncallPersonName['last'].'"></div>
									';
								} else {
									$onCallToDisplay = '';
								}
							}
							
							
							// Check the array of vacation requests for the month and see if any match this day
							foreach ($thisMonthResults as $val)
							{
								$dayValue_allDatesFromRequest = $formattedDayNumber = "";
								
								$allDatesFromRequest = getDatesFromRange($val['startDate'], $val['endDate']);
								
								foreach ($allDatesFromRequest as $val2)
								{
									// Set the day number for each day in the array
									$dayValue_allDatesFromRequest = substr($val2, -5);
									$formatted_monthDay = sprintf("%02d", $first['mon'])."-".sprintf("%02d", $i);
									
									if ($dayValue_allDatesFromRequest == $formatted_monthDay && $dayOfWeek != 0 && $dayOfWeek != 6)
									{
										$vacEdit = "";
										$userIDtoFetch = $val['user'];
										
										try {
											$query2 = $conn->prepare("SELECT * FROM vacations_users WHERE uid = '$userIDtoFetch'");
											$query2->execute();
											$userDetails = $query2->fetch();
										} catch(PDOException $e) {
											echo $e->getMessage();
										}
										
										$randomNum = rand(1, 9999).$userDetails['uid'];
										
										// See if admin logged in.
										// If so, allow vacation edits
										if ($loggedIn && $isAdmin)
										{
											$modalBuild = new buildModal('update', $randomNum, $val['requestID']);
											$singleModal = $modalBuild->getModal();
											
											$vacEdit = '
												<i class="fa fa-pencil-square-o" data-toggle="modal" data-target="#modal-'.$randomNum.'" data-tooltip="tooltip" title="Edit"></i>
												'.$singleModal.'
											';
										}
										
										if ($val['approval'] == 1)
										{
											$approvedSymbol = '<i class="fa fa-star" data-tooltip="tooltip" title="Approved"></i>';
										} else {
											// Not Approved Yet
											$approvedSymbol = '<i class="fa fa-star-o" data-tooltip="tooltip" title="Not Approved"></i>';
										}
										
										$namesToDisplay .= '
											<div class="vacPerson" style="background-color: #'.$userDetails['color'].'">'.$approvedSymbol.' <span data-tooltip="tooltip" title="'.$val['description'].'">'.$userDetails['first'].' '.$userDetails['last'].'</span> '.$vacEdit.'</div>
										';
									} else {
										// It's a weekend, do nothing.
									}
								}
								unset($val2);
							}
							unset($val);
							
							// Print the day box and it's contents
                            echo '  <div class="col-md-1 ' . $style . '">'.$holidayDisplay.$onCallToDisplay.'<div class="daynumberBox"><div class="dayNum">' . $i . '</div></div>' . $namesToDisplay . '</div>' . "\n";
							
							$namesToDisplay = "";
                        }
                        
                        if($last['wday'] < 6)
                        {
                            for($i = $last['wday']; $i < 6; $i++)
                            {
                                echo '  <div class="col-md-1 inactive">&nbsp;</div>' . "\n";
                            }
                        }
                    ?>
                </div>
                
            </div>
        </div>
    </section>
    
    <?php
	    $modalBuild = new buildModal('new', null, null, $userID, $isAdmin);
		echo $modalBuild->getModal();
		
		$modalBuild = new buildModal('modifyUser', null, null, $userID);
		echo $modalBuild->getModal();
		
		$modalBuild = new buildModal('forgotPW', null, null);
		echo $modalBuild->getModal();
	
		include('includes/footerScripts.php');
	?>
    
    
  </body>
</html>