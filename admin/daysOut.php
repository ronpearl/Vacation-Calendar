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
	
	require('../settings.php');
	include_once('../includes/baseConnection.php');
	include_once('../includes/dateFunctions.php');
	date_default_timezone_set('America/Phoenix');
	
	// Check to see if admin is viewing page.
	// Otherwise send them back home
	if (!$isAdmin)
	{
		header('Location: '.$siteRoot);
	}
	
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Vacation Calendar - ADMIN - GCU Web Design</title>
        
        <!-- Bootstrap -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link href="../css/main.css" rel="stylesheet">
        <link href="../css/calendar.css" rel="stylesheet">
        <link href="../css/bootstrap-colorpicker.css" rel="stylesheet">
        
        <link type="text/css" rel="stylesheet" href="../css/bootstrap-datetimepicker.css">
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
    
    	<?php
			include('includes/adminNav.php');
		?>
		
        
        <section>
        	<div class="container">
            	<div class="col-md-12">
	            	<h2>Admin - Days Out</h2>
                    <br>
              	</div>
            </div>
        </section>
  		
        
        <?php
			$firstDayDate = $lastDayDate = $approval = "";
			$approval_array = array("0" => "No", "1" => "Yes");
			$approval = 1;
			
			// By default, get vacations for this month.
			// Check to see if date range was submitted.
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['startDate']))
			{
				$firstDayDate = $_POST['startDate'];
				$lastDayDate = $_POST['endDate'];
				$approval = $_POST['approval'];
			} else {
				$today = new DateTime('now');
				$todayFormatted = $today->format('Y-m-d');
				$firstDayDate = date('Y-m-01', strtotime($todayFormatted));
				$lastDayDate = date('Y-m-t', strtotime($todayFormatted));				
			}
		?>
        
        
        <section>
        	<div class="container">
            	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                    <div class="col-md-2 col-md-offset-2">
                  		<div class="form-group">
                        	<label for="startDate">Start Date:</label>
                            <input type="text" class="form_datetime form-control" name="startDate" value="<?php echo $firstDayDate; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                  		<div class="form-group">
                        	<label for="endDate">End Date:</label>
                            <input type="text" class="form_datetime form-control" name="endDate" value="<?php echo $lastDayDate; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                  		<div class="form-group">
                        	<label for="approval">Approved:</label>
                            <select name="approval" class="form-control">
                            	<?php
									for($i = 0; $i <= 1; $i++)
									{						
										if ($approval == $i)
										{
											echo '<option value="'.$i.'" selected>'.$approval_array[$i].'</option>';
										} else {
											echo '<option value="'.$i.'">'.$approval_array[$i].'</option>';
										}
									}
								?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                    	<label for="submit">&nbsp;</label><br>
                  		<input type="submit" name="submit" class="btn btn-primary" value="Search">
                    </div>
                </form>
        	</div>
     	</section>
        
    	
        <br><br>
        
        
        <section>
        	<div class="container">
            	<div class="col-md-4 col-md-offset-4">
                	<?php
						// Get all instances of vacations during this month
						$thisMonthResults = $allDatesFromRequest = "";
						$userID_array = array();
						try {
							$db = new baseConnection();
							$conn = $db->getConn();
							$query = $conn->prepare("SELECT * FROM vacations_requests WHERE (startDate BETWEEN '$firstDayDate' AND '$lastDayDate' OR endDate BETWEEN '$firstDayDate' AND '$lastDayDate') AND approval = '$approval' ORDER BY user");
							$query->execute();
							$thisMonthResults = $query->fetchAll();
							
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
						
						foreach($thisMonthResults as $val)
						{
							// Check the dates in this request and make sure they are from the selected date range.
							$allDatesFromRequest = getDatesFromRange($val['startDate'], $val['endDate']);
							$dateCount = 0;
							foreach ($allDatesFromRequest as $singleDate)
							{
								// Make sure the date is not on a weekend
								if (!dateIsWeekend($singleDate))
								{
									if ($singleDate >= $firstDayDate && $singleDate <= $lastDayDate)
									{
										$dateCount++;
									}
								}
							}
							unset($singleDate);
							
							
							// Make sure user is in $userID_array and then add the number of days they have during this period
							if (array_key_exists($val['user'], $userID_array))
							{
								$userID_array[$val['user']] = $userID_array[$val['user']] + $dateCount;
							} else {
								$userID_array[$val['user']] = $dateCount; 
							}
						}
						unset($val);
						
						
						echo '
							<table class="userTable">
								<tbody>
									<tr>
										<th>Name</th>
										<th class="text-center">Days Out</th>
									</tr>
						';
						
						// Now go through the $userID_array and display the info
						foreach ($userID_array as $key => $val)
						{
							try {
								$query = $conn->prepare("SELECT first, last FROM vacations_users WHERE uid = '$key' LIMIT 1");
								$query->execute();
								$singleUser = $query->fetch();
							} catch(PDOException $e) {
								echo $e->getMessage();
							}
							
							echo '
								<tr>
									<td>'.$singleUser['first'].' '.$singleUser['last'].'</td>
									<td class="text-center">'.$val.'</td>
								</tr>
							';
						}
						
						echo '
								</tbody>
							</table>
						';
					?>
	        	</div>
          	</div>
     	</section>
        
        <br><br>
        
        <section>
        	<div class="container">
                <div class="col-md-12">
                	<h2>Sourrounding Month Counts</h2>
                	<?php
						$today = new DateTime('now');
						$thisMonth = $today->format('n');
						$surroundingMonthsStartAndEnd = array();
						
						// Get previous 5 months
						for ($i = 5; $i >= 1; $i--) {
							$startofMonth = date("Y-m-d", strtotime( date( 'Y-m-01' )." -$i months"));
							$endofMonth = date("Y-m-t", strtotime( date( 'Y-m-01' )." -$i months"));
							$surroundingMonthsStartAndEnd[$startofMonth] = $endofMonth;
						}
						
						// Current Month
						$startofMonth = date("Y-m-d", strtotime( date( 'Y-m-01' )));
						$endofMonth = date("Y-m-t", strtotime( date( 'Y-m-01' )));
						$surroundingMonthsStartAndEnd[$startofMonth] = $endofMonth;
						
						// Get next 6 months
						for ($i = 1; $i <= 6; $i++) {
							$startofMonth = date("Y-m-d", strtotime( date( 'Y-m-01' )." +$i months"));
							$endofMonth = date("Y-m-t", strtotime( date( 'Y-m-01' )." +$i months"));
							$surroundingMonthsStartAndEnd[$startofMonth] = $endofMonth;
						}
						
						
						// Create header of table for the $surroundingMonthsStartAndEnd
						echo '
							<table class="userTable">
								</tbody>
									<th> </th>
						';
						
						foreach ($surroundingMonthsStartAndEnd as $key => $val)
						{
							$iteratedDateTime = new DateTime($key);
							$iteratedMonth = $iteratedDateTime->format('M');
							$iteratedYear = $iteratedDateTime->format('Y');
							
							echo '<th class="text-center">'.$iteratedMonth.'<br>'.$iteratedYear.'</th>';
						}
						
						echo '<th class="text-center">Total</th>';
						
						
						// For each person, get their data
						try {
							$query = $conn->prepare("SELECT * FROM vacations_users ORDER BY first");
							$query->execute();
							$allUsers = $query->fetchAll();
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
						
						foreach ($allUsers as $row)
						{
							$totalCountPerUser = 0;
							
							echo '
								<tr>
									<td>'.$row['first'].' '.$row['last'].'</td>
							';
							
							// Now go through the date ranges and get the data for each person
							foreach ($surroundingMonthsStartAndEnd as $key => $val)
							{
								try {
									$query2 = $conn->prepare("SELECT * FROM vacations_requests WHERE user = '".$row['uid']."' AND (startDate BETWEEN '$key' AND '$val' OR endDate BETWEEN '$key' AND '$val') ORDER BY user");
									$query2->execute();
									$thisMonthResults = $query2->fetchAll();
									
								} catch(PDOException $e) {
									echo $e->getMessage();
								}
								
								if(count($thisMonthResults) > 0)
								{
									$dateCount = 0;
									
									foreach($thisMonthResults as $val2)
									{
										// Check the dates in this request and make sure they are from the selected date range.
										$allDatesFromRequest = getDatesFromRange($val2['startDate'], $val2['endDate']);
										foreach ($allDatesFromRequest as $singleDate)
										{
											// Make sure the date is not on a weekend
											if (!dateIsWeekend($singleDate))
											{
												if ($singleDate >= $key && $singleDate <= $val)
												{
													$dateCount++;
												}
											}
										}
										unset($singleDate);
									}
									unset($val2);
									
									$totalCountPerUser+= $dateCount;
									
									echo '
										<td class="text-center">'.$dateCount.'</td>
									';
								} else {
									// No entries found for this month
									echo '
										<td class="text-center" style="color: #aaa8a8;">0</td>
									';
								}
							}
							unset($key);
							unset($val);
							
							echo '
									<td class="text-center" style="background-color: #fbfbfb;">'.$totalCountPerUser.'</td>
								</tr>
							';
						}
						unset($row);
						
						echo '
								</tbody>
							</table>
						';
					?>
                </div>
            </div>
		</section>
        
        <br><br>
        
    	
    	<!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        
        <!-- Compiled plugins -->
        <script src="../js/bootstrap.min.js"></script>
    	
        <!-- Date Time Picker : http://www.malot.fr/bootstrap-datetimepicker/ -->
		<script src="../includes/bootstrap-datetimepicker.js"></script>
        <script type="text/javascript">
            $(".form_datetime").datetimepicker({
                format: 'yyyy-mm-dd',
                todayBtn: true,
                autoclose: true,
                minView: 'month'
            });
        </script>
        
        <!-- Enable Tooltips -->
		<script>
        $(function () {
          $('[data-tooltip="tooltip"]').tooltip()
        })
        </script>
        
    </body>
</html>
