<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<!-- Compiled plugins -->
<script src="js/bootstrap.min.js"></script>

<!-- COLORPICKER: http://mjolnic.com/bootstrap-colorpicker/ -->
<script src="js/bootstrap-colorpicker.js"></script>

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
</script>

<!-- Date Time Picker : http://www.malot.fr/bootstrap-datetimepicker/ -->
<script src="includes/bootstrap-datetimepicker.js"></script>
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

<!-- LOGIN Functionality -->
<script type="text/javascript">
$("document").ready(function(){
  $(".form-signin").submit(function(){
	var data = {
	  "action": "test"
	};
	data = $(this).serialize() + "&" + $.param(data);
	
	$(".working").css('display', 'block');
	$("#error").html("<div class='alert alert-warning' role='alert'>Working...</div>");
	
	setTimeout(function() {	
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "includes/login.php", 
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
					$("#error").html("<div class='alert alert-danger' role='alert'>Invalid email and/or password</div>");
				} else {
					$("#error").html("<div class='alert alert-success' role='alert'>Being re-directed...</div>");
					window.location.href = "index.php";
				}
			},
			error: function() {
				$("#error").html("<div class='alert alert-danger' role='alert'>Error in processing</div>");
			}
		});
		
		$(".working").css('display', 'none');
	}, 2800);
	
	return false;
  });
});
</script>


<!-- New Vacation Request -->
<script type="text/javascript">
$("document").ready(function(){
  $(".form-newRequest").submit(function(){
	var data = {
	  "action": "test"
	};
	data = $(this).serialize() + "&" + $.param(data);
	
	$("#newReqError").html("<div class='alert alert-warning' role='alert'>Working...</div>");
	
	$.ajax({
		type: "POST",
		dataType: "json",
		url: "updaters/newVacRequest.php", 
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
				$("#newReqError").html("<div class='alert alert-danger' role='alert'>Request must be between 7 and 90 days out.<br>Double-check your date submission.</div>");
			} else {
				window.location.href = "index.php";
			}
		},
		error: function(data) {
			$("#newReqError").html("<div class='alert alert-danger' role='alert'>Error in processing</div>");
		}
	});
	
	return false;
  });
});
</script>



<!-- Password Reset -->
<script type="text/javascript">
$("document").ready(function(){
  $(".forgotPWForm").submit(function(){
	var data = {
	  "action": "test"
	};
	data = $(this).serialize() + "&" + $.param(data);
	
	$("#pwResetError").html("<div class='alert alert-warning' role='alert'>Working...</div>");
	
	$.ajax({
		type: "POST",
		dataType: "json",
		url: "updaters/pwResetRequest.php", 
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
				$("#pwResetError").html("<div class='alert alert-danger' role='alert'>There was a problem submitting your request.</div>");
			} else {
				$("#pwResetError").html("<div class='alert alert-success' role='alert'>Please check your email at "+data['email']+" for further instruction.</div>");
			}
		},
		error: function() {
			$("#pwResetError").html("<div class='alert alert-danger' role='alert'>Error in processing</div>");
		}
	});
	
	return false;
  });
});
</script>



<script>
	// ADD SLIDEDOWN ANIMATION TO DROPDOWN //
	$('#loginDropdown').on('show.bs.dropdown', function(e){
		$(this).find('.dropdown-menu').first().stop(true, true).slideDown();
	});
	
	// ADD SLIDEUP ANIMATION TO DROPDOWN //
	$('#loginDropdown').on('hide.bs.dropdown', function(e){
		$(this).find('.dropdown-menu').first().stop(true, true).slideUp();
	});
</script>