<?php
	// Check for logged in status
	if ($loggedIn)
	{
		echo '
			<li><a href="#newRequestModal" data-toggle="modal" data-target="#newRequestModal">New Request</a></li>
			<li><a href="#modal-userInfo" data-toggle="modal" data-target="#modal-userInfo">Profile</a></li>
		';
		
		if ($isAdmin)
		{
			echo '
				<li role="presentation" class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
						Admin <span class="caret"></span>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="admin/">Users</a></li>
						<li><a href="admin/daysOut.php">Days Out</a></li>
						<li><a href="admin/oncallRotation.php">On Call</a></li>
					</ul>
				</li>
			';
		}
		
		echo '
			<li><a href="includes/logout.php">Log Out</a></li>
		';
		
	} else {
		echo '
			<li class="dropdown" id="loginDropdown">
				<a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
				<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
					<div id="error"></div>
					<form class="form-signin">
						<input id="user_username" type="email" name="email" size="30" placeholder="Enter Email"/>
						<input id="user_password" type="password" name="pass" size="30" placeholder="Enter Password"/>
						<div class="pull-right"><a href="#modal-pwreset" data-toggle="modal" data-target="#modal-pwreset" class="forgotPWLink">Forgot Password</a></div>
						<input class="btn btn-primary" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="submit" name="commit" value="Sign In" />
					</form>
				</div>
			</li>
		';
	}
?>
