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
        <title>Vacation Calendar - ADMIN</title>
        
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
    	
        <style type="text/css">
			.fa.fa-plus-square {
				color: #489947;
				float: right;
				font-size: 30px;
				position: relative;
				top: 27px;
				cursor: pointer;
			}
		}
		</style>
                
        <section>
        	<div class="container">
            	<div class="col-md-12">
                	<a href="#"><i class="fa fa-plus-square" data-toggle="modal" data-target="#newUserModal" data-tooltip="tooltip" title="Add New User"></i></a>
	            	<h2>Admin - User List</h2>
                    <br>
              	</div>
            </div>
        </section>
        
        
        <section>
        	<div class="container">
				<?php
                    try {
                        $db = new baseConnection();
                        $conn = $db->getConn();
                        $query = $conn->prepare("SELECT * FROM vacations_users ORDER BY first ASC");
                        $query->execute();
                        $allUsers = $query->fetchAll();
                    } catch(PDOException $e) {
                        echo $e->getMessage();
                    }
                    
                    echo '
                        <table class="userTable">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
									<th></th>
									<th></th>
                                </tr>
                    ';
                    
                    foreach ($allUsers as $userInfo)
                    {
                        $uid = $userInfo['uid'];
                        $fName = $userInfo['first'];
                        $lName = $userInfo['last'];
                        $email = $userInfo['email'];
						$userColor = $userInfo['color'];
                        
                        echo '
                            <tr>
                                <td>'.$uid.'</td>
                                <td>'.$fName.' '.$lName.'</td>
                                <td>'.$email.'</td>
                                <td></td>
                                <td><i class="fa fa-pencil-square-o" data-toggle="modal" data-target="#userModal" data-tooltip="tooltip" title="Edit" data-usernumber="'.$uid.'" data-firstname="'.$fName.'" data-lastname="'.$lName.'" data-emailaddy="'.$email.'" data-color="'.$userColor.'" style="color: #ff0000; cursor: pointer;"></i></td>
                            </tr>
                        ';
                    }
                    
                    echo '
                            </tbody>
                        </table>
                    ';
                ?>
        	</div>
        </section>
		
        <!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        
        <!-- Compiled plugins -->
        <script src="../js/bootstrap.min.js"></script>
        
        
        <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="User Information" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Edit User - <span id="showUserName"></span></h4>
              </div>
              <div class="modal-body">
                <form method="post" action="updateUser.php">
                	<div class="form-group">
                        <label for="firstName" class="control-label">First Name</label>
                        <input type="text" class="form-control" name="firstName" id="firstNameOnForm" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="control-label">Last Name</label>
                        <input type="text" class="form-control" name="lastName" id="lastNameOnForm" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="text" class="form-control" name="email" id="emailOnForm" required>
                    </div>
                    <div class="form-group">
                        <label for="color" class="control-label">Color</label>
                        <input type="text" class="form-control" name="color" id="userColorpicker" required>
                    </div>
                  <input type="hidden" name="userID" id="hiddenUserID" value="">
                  <input type="submit" class="btn btn-primary" name="submit" value="Update">
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        <!-- COLORPICKER: http://mjolnic.com/bootstrap-colorpicker/ -->
		<script src="../js/bootstrap-colorpicker.js"></script>
        
		<script>
			$('#userModal').on('show.bs.modal', function (event) {
			  var button = $(event.relatedTarget) // Button that triggered the modal
			  var userName = button.data('firstname') + " " + button.data('lastname');
			  var userID = button.data('usernumber');
			  var userEmail = button.data('emailaddy');
				  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
				  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
			  var modal = $(this)
			  
			  modal.find('#showUserName').text(userName);
			  modal.find('#firstNameOnForm').val(button.data('firstname'));
			  modal.find('#lastNameOnForm').val(button.data('lastname'));
			  modal.find('input[id="hiddenUserID"]').val(userID);
			  modal.find('#emailOnForm').val(userEmail);
			  modal.find('#userColorpicker').val("#"+button.data('color'));
			})
		</script>
        
        
        <div class="modal fade" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="User Information" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Add New User</h4>
              </div>
              <div class="modal-body">
                <form method="post" action="addUser.php">
                	<div class="form-group">
                        <label for="firstName" class="control-label">First Name</label>
                        <input type="text" class="form-control" name="firstName" id="firstNameOnForm" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="control-label">Last Name</label>
                        <input type="text" class="form-control" name="lastName" id="lastNameOnForm" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="text" class="form-control" name="email" id="emailOnForm" required>
                    </div>
                    <div class="form-group">
                        <label for="color" class="control-label">Color</label>
                        <input type="text" class="form-control" name="color" id="userColorpicker2" required>
                    </div>
                  <input type="hidden" name="userID" id="hiddenUserID" value="">
                  <input type="submit" class="btn btn-primary" name="submit" value="Update">
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        
        
        <script>
			$(function(){
				$('#userColorpicker').colorpicker({
					customClass: 'colorpicker-2x',
					sliders: {
						saturation: {
							maxLeft: 200,
							maxTop: 200
						},
						hue: {
							maxTop: 200
						},
						alpha: {
							maxTop: 200
						}
					}
				});
			});
			
			$(function(){
				$('#userColorpicker2').colorpicker({
					customClass: 'colorpicker-2x',
					sliders: {
						saturation: {
							maxLeft: 200,
							maxTop: 200
						},
						hue: {
							maxTop: 200
						},
						alpha: {
							maxTop: 200
						}
					}
				});
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
