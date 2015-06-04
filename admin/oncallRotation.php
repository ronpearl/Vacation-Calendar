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
	
	$db = new baseConnection();
	
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
        
		<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
        
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
            	<div class="col-md-8">
	            	<h2>Admin - On Call Rotation</h2>
                    <br>
              	</div>
                <div class="col-md-4">
                	<?php
						if ($onCallService)
						{
							// Allow for users that are on call to be reset
							echo '
								<div class="oncallSelect">
									<form method="post" action="resetOncallUsers.php">
										<div class="form-group">
											<label for="oncallSelectDropdown">Reset On Call People</label>
											<br>
											<select name="oncallSelectDropdown[]" id="multi-dropdown" multiple="multiple" class="form-control">
							';
							
							try {
								$conn4 = $db->getConn();
								$query4 = $conn4->prepare("SELECT * FROM vacations_users ORDER BY first ASC");
								$query4->execute();
								$allUsers = $query4->fetchAll();
							} catch(PDOException $e) {
								echo $e->getMessage();
							}
							
							foreach ($allUsers as $userInfo)
							{
								echo '<option value="'.$userInfo['uid'].'">'.$userInfo['first'].' '.$userInfo['last'].'</option>';
							}
							unset($userInfo);
							
							echo '
											</select>
											<input type="submit" class="btn btn-primary" name="submit" value="Reset" style="display: inline-block;">
										</div>
									</form>
								</div>
							';
						}
					?>
                </div>
            </div>
        </section>
        
        
        <section>
        	<div class="container">
				<?php
					// Check to see if oncall service is turned on.
					// If not, point admin to location to have it activated in settings file
					if ($onCallService)
					{
						$today = getdate();
						$thisYear = $today['year'];
						
						echo '
							<table class="userTable">
								<tbody>
									<tr>
										<th>Week Number</th>
										<th>Week of Date</th>
										<th>Name</th>
										<th></th>
										<th></th>
									</tr>
						';
					
						try {
							$conn = $db->getConn();
							$query = $conn->prepare("SELECT * FROM vacations_oncall ORDER BY week_number ASC");
							$query->execute();
							$allOncall = $query->fetchAll();
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
						
						foreach ($allOncall as $val)
						{
							try {
								$conn = $db->getConn();
								$query = $conn->prepare("SELECT * FROM vacations_users WHERE uid = '".$val['user']."'");
								$query->execute();
								$userInfo = $query->fetch();
							} catch(PDOException $e) {
								echo $e->getMessage();
							}
							
							$uid = $userInfo['uid'];
							$fName = $userInfo['first'];
							$lName = $userInfo['last'];
							$email = $userInfo['email'];
							$userColor = $userInfo['color'];
							
							$formattedWeekNumber = sprintf("%02d", $val['week_number']);
							$weekOfDate = date("M jS", strtotime($thisYear."W".$formattedWeekNumber));
							
							echo '
								<tr>
									<td>'.$val['week_number'].'</td>
									<td>'.$weekOfDate.'</td>
									<td>'.$fName.' '.$lName.'</td>
									<td></td>
									<td><i class="fa fa-pencil-square-o" data-toggle="modal" data-target="#oncallModal" data-tooltip="tooltip" title="Edit" data-usernumber="'.$uid.'" data-firstname="'.$fName.'" data-lastname="'.$lName.'" data-weekofdate="'.$weekOfDate.'" data-weeknumber="'.$val['week_number'].'" style="color: #ff0000; cursor: pointer;"></i></td>
								</tr>
							';
						}
						
						echo '
								</tbody>
							</table>
							<br><br>
						';
					} else {
						echo "<div class='alert alert-danger' role='alert'>Please update the settings.php file to turn the On Call services on.</div>";
					}
                ?>
        	</div>
        </section>
        
        
        <!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        
        <!-- Compiled plugins -->
        <script src="../js/bootstrap.min.js"></script>
        
        
        <div class="modal fade" id="oncallModal" tabindex="-1" role="dialog" aria-labelledby="On Call Modal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Update On Call for <span id="showWeekOfDate"></span></h4>
              </div>
              <div class="modal-body">
              	<strong>Currently in this spot is:</strong> <span id="showCurrentName"></span>
                <br><br>
                <form method="post" action="updateOncall.php">
                	<div class="form-group">
                        <label for="onCallPerson" class="control-label">Select New On Call Person</label>
                        <select name="onCallPerson" id="dropdownList" class="form-control">
                        	
                        </select>
                    </div>
                  <input type="hidden" name="weekNumber" id="hiddenWeekNumber" value="">
                  <input type="submit" class="btn btn-primary" name="submit" value="Update" id="updateBTN">
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        
        <script>
			$('#oncallModal').on('show.bs.modal', function (event) {
				var button = $(event.relatedTarget) // Button that triggered the modal
				var userName = button.data('firstname') + " " + button.data('lastname');
				var currentUserID = button.data('usernumber');
				var weekNumber = button.data('weeknumber');
				var weekOfDate = button.data('weekofdate');
				
				
				// Do AJAX call to fill dropdown select
				var data = {
				  "action": "test",
				  "weekNum": weekNumber,
				  "userID": currentUserID
				};
				data = $.param(data);
				
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "onCallSelectCreator.php", 
					data: data,
					success: function(data) {
						// Check the results and do things from here
						if (data["action"] == "fail")
						{
							$("#updateBTN").prop("disabled",true);
						} else {
							// Show Dropdown
							modal.find('#dropdownList').html(data['dropdownInformation'])
						}
					},
					error: function() {
						$("#updateBTN").prop("disabled",true);
					}
				});
				
				
				var modal = $(this)
				
				modal.find('#showWeekOfDate').text(weekOfDate);
				modal.find('#showCurrentName').text(userName);
				modal.find('input[id="hiddenWeekNumber"]').val(weekNumber);
			})
		</script>
        
        
        
		<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
        <!-- Initialize the multi-select plugin -->
        <script type="text/javascript">
            $(document).ready(function() {
                $('#multi-dropdown').multiselect({
					disableIfEmpty: true,
					maxHeight: 200,
					nonSelectedText: 'This will RESET your current list',
					includeSelectAllOption: true,
					selectAllValue: 'select-all'
				});
			});
        </script>
        
        
        <?php
			$db = null;
		?>
   	</body>
</html>