<?php
	$pw_verify_code = "";
	
	if (isset($_GET['pwvar']))
	{
		$pw_verify_code = $_GET['pwvar'];
	}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>PW Reset - Vacation Calendar - GCU Web Design</title>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="../css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Web Design Vacation Calendar</a>
           	</div>
      	</div>
    </nav>

	<section>
    	<div class="row">
	    	<div class="col-md-4 col-md-offset-4">
        		<div class="pwResetBox">
                	<h3 style="margin-top: 0;">Reset Your Password</h3>
                    <hr>
                	<div id="reset_error"></div>
                	<form class="pw_reset">
                    	<div class="form-group">
                        	<label for="email">Enter Your Email Address</label>
                            <input type="email" name="email" class="form-control input-md" required>
                        </div>
                        <div class="form-group">
                        	<label for="verifyCode">Verification Code</label>
                            <input type="text" name="verifyCode" value="<?php echo $pw_verify_code; ?>" class="form-control input-md" required>
                        </div>
                        <div class="form-group">
                        	<label for="passw">New Password</label>
                            <input type="password" name="passw" class="form-control input-md" required>
                        </div>
                        <input id="new-class-btn" type="submit" class="btn btn-primary" value="Process Reset">
                    </form>
                </div>
            </div>
        </div>
    </section>
	
    
    <!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    
    <!-- Compiled plugins -->
    <script src="../js/bootstrap.min.js"></script>

    
    <!-- Password Reset -->
	<script type="text/javascript">
    $("document").ready(function(){
      $(".pw_reset").submit(function(){
        var data = {
          "action": "test"
        };
        data = $(this).serialize() + "&" + $.param(data);
        
        $("#reset_error").html("<div class='alert alert-warning' role='alert'>Working...</div>");
        
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "updatePW.php", 
            data: data,
            success: function(data) {
                /* 
                    Hiding - Only for testing resulting data
                    $("#error").html(
                      data["json"]
                    );
                */
                
                // Check the results and do things from here
                if (data["action"] == "fail")
                {
                    $("#reset_error").html("<div class='alert alert-danger' role='alert'>There was a problem submitting your request.</div>");
                } else {
                    $("#reset_error").html('<div class="alert alert-success" role="alert">Please <a href="../index.php">log in</a> using your new credentials.</div>');
                }
            },
            error: function() {
                $("#reset_error").html("<div class='alert alert-danger' role='alert'>Error in processing</div>");
            }
        });
        
        return false;
      });
    });
    </script>
    
    
	</body>
</html>