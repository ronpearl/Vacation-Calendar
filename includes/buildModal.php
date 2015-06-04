<?php
	/*
	*  Build Modal based upon values passed
	*  ------------------------------------
	*	Modal Type, Random Number for Modal Identity, requestID for DB call, user id for new requests, admin status
	*/
	class buildModal extends baseConnection
	{
		protected $randomNumber;
		protected $requestID;
		protected $modalType;
		protected $userID;
		protected $isAdmin;
		
		protected $modalBuilt;
		
		public function __construct($modalType, $randomNumber, $request_ID, $userID = null, $isAdmin = false)
		{
			$this->modalType = $modalType;
			$this->randomNumber = $randomNumber;
			$this->requestID = $request_ID;
			$this->userID = $userID;
			$this->isAdmin = $isAdmin;
			
			$this->doBuild($this->modalType);
		}
		
		
		/*
		*	Create the modal
		*/
		public function doBuild($type)
		{
			if ($type == 'update')
			{		
				$approved = $notApproved = "";
				
				// Get information based upon the requestID
				try {
					$db = new baseConnection();
					$conn = $db->getConn();
					$query = $conn->prepare("SELECT * FROM vacations_requests WHERE requestID = '".$this->requestID."'");
					$query->execute();
					$requestInfo = $query->fetch();
				} catch(PDOException $e) {
					echo $e->getMessage();
				}
				
				if ($requestInfo['approval'] == 0)
				{
					$notApproved = "selected";
				} else if ($requestInfo['approval'] == 1) {
					$approved = "selected";
				}
				
				
				$this->modalBuilt = '
					<div class="modal fade" id="modal-'.$this->randomNumber.'" role="dialog" aria-labelledby="vacationModal" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<a href="updaters/deleteReq.php?requestID='.$this->requestID.'"><i class="fa fa-trash" data-tooltip="tooltip" title="Delete Request"></i></a><h4 class="modal-title" id="myModalLabel">Update Vacation</h4>
						  </div>
						  <div class="modal-body">
							<form method="post" action="updaters/updateVacationRequest.php">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="startDate">Start Date</label>
											<input type="text" name="startDate" value="'.$requestInfo['startDate'].'" readonly class="form_datetime form-control input-md" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="endDate">End Date</label>
											<input type="text" name="endDate" value="'.$requestInfo['endDate'].'" readonly class="form_datetime form-control input-md" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="requestDate">Request Date</label>
											<input type="text" name="requestDate" value="'.$requestInfo['requestDate'].'" readonly class="form_datetime form-control input-md" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="approved">Approved</label>
											<select name="approved" class="form-control">
												<option value="0" '.$notApproved.'>Not Approved</option>
												<option value="1" '.$approved.'>Approved</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="description">Description</label>
											<textarea class="form-control input-md" name="description" maxlength="200" placeholder="Max 200 characters">'.$requestInfo['description'].'</textarea>
										</div>
									</div>
								</div>
								<input type="hidden" name="requestID" value="'.$this->requestID.'">
								<input id="new-class-btn" type="submit" class="btn btn-primary" value="Submit">
							</form>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  </div>
						</div>
					  </div>
					</div>
				';
			} else if ($type == "new") {
				$adminFields = "";
				$today = date("Y-m-d");
				
				if ($this->isAdmin)
				{
					$overrideTimeframes = '
						<div class="form-group">
							<label for="overrideTimeframes">Override Timeframe Limitations?</label><br>
							<input type="checkbox" id="overrideTimeframes" name="overrideTimeframes" value="true"> Yes
						</div>
					';
					
					// Also get list of users so the Admin can add for other people
					try {
						$db = new baseConnection();
						$conn = $db->getConn();
						$query = $conn->prepare("SELECT * FROM vacations_users ORDER BY first ASC");
						$query->execute();
						$allUsers = $query->fetchAll();
					} catch(PDOException $e) {
						echo $e->getMessage();
					}
					
					$adminFields = '
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="userSelect">Who is this for?</label>
									<select name="userSelect" class="form-control">
					';
					
					foreach ($allUsers as $row)
					{
						$uid = $row['uid'];
						$fName = $row['first'];
						$lName = $row['last'];
						$email = $row['email'];
						
						$adminFields .= '
							<option value="'.$uid.'">'.$fName.' '.$lName.'</option>
						';
					}
					
					$adminFields .= '
									</select>
								</div>
							</div>
							<div class="col-md-6">
								'.$overrideTimeframes.'
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<strong>Approved?</strong><br>
									<label for="vacApprovalYes"><input type="radio" id="vacApprovalYes" name="vacApproval" value="vacApproved" checked> Yes</label> &nbsp;&nbsp; 
									<label for="vacApprovalNo"><input type="radio" id="vacApprovalNo" name="vacApproval" value="vacNotApproved"> No
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<strong>Send Email Confirmation?</strong><br>
									<label for="emailConfirmYes" style="font-weight: normal;"><input type="radio" id="emailConfirmYes" name="emailConfirm" value="send_email" checked> Yes</label> &nbsp;&nbsp; 
									<label for="emailConfirmNo" style="font-weight: normal;"><input type="radio" id="emailConfirmNo" name="emailConfirm" value="dont_send_email"> No</label>
								</div>
							</div>
						</div>
						<input type="hidden" value="yes" name="adminSubmission">
					';
					
				} else {
					// User is not an admin, so we need to add the default inputs
					$adminFields = '
						<input type="hidden" value="send_email" name="emailConfirm">
						<input type="hidden" value="vacNotApproved" name="vacApproval">
					';
				}
				
				$this->modalBuilt = '
					<div class="modal fade" id="newRequestModal" role="dialog" aria-labelledby="vacationModalNew" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">New Vacation Request</h4>
						  </div>
						  <div class="modal-body">
						  	<div id="newReqError"></div>
							<form class="form-newRequest">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="startDate">Start Date</label>
											<input type="text" name="startDate" value="'.$today.'" readonly class="form_datetime form-control input-md" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="endDate">End Date</label>
											<input type="text" name="endDate" value="'.$today.'" readonly class="form_datetime form-control input-md" required>
										</div>
									</div>
								</div>
								'.$adminFields.'
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="shortDescr">Description</label>
											<textarea class="form-control input-md" name="shortDescr" placeholder="Max 200 characters"></textarea>
										</div>
									</div>
								</div>
								<input type="hidden" name="requestDate" value="'.$today.'">
								<input type="hidden" name="userID" value="'.$this->userID.'">
								
								<input id="new-class-btn" type="submit" class="btn btn-primary" value="Submit">
							</form>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  </div>
						</div>
					  </div>
					</div>
				';
			} else if ($type == "modifyUser") {
				// Get information based upon the userID
				try {
					$db = new baseConnection();
					$conn = $db->getConn();
					$query = $conn->prepare("SELECT * FROM vacations_users WHERE uid = '".$this->userID."'");
					$query->execute();
					$requestInfo = $query->fetch();
				} catch(PDOException $e) {
					echo $e->getMessage();
				}
				
				$this->modalBuilt = '
					<div class="modal fade" id="modal-userInfo" role="dialog" aria-labelledby="userInfoModal" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<h4 class="modal-title" id="myModalLabel">Update Your Profile</h4>
						  </div>
						  <div class="modal-body">
							<form method="post" action="updaters/updateVacationUserInfo.php">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="fName">First Name</label>
											<input type="text" name="fName" value="'.$requestInfo['first'].'" class="form-control input-md" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="lName">Last Name</label>
											<input type="text" name="lName" value="'.$requestInfo['last'].'" class="form-control input-md" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="email">Email Address</label>
											<input type="email" name="email" value="'.$requestInfo['email'].'" class="form-control input-md" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="color">Color</label>
											<input type="text" name="color" id="userColorpicker" value="'.$requestInfo['color'].'" class="form-control input-md" required>
										</div>
									</div>
								</div>
								<input type="hidden" name="submittedUserID" value="'.$this->userID.'">
								<input id="new-class-btn" type="submit" class="btn btn-primary" value="Submit">
							</form>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  </div>
						</div>
					  </div>
					</div>
				';
			} else if ($type == "forgotPW") {
				$this->modalBuilt = '
					<div class="modal fade" id="modal-pwreset" role="dialog" aria-labelledby="pwreset" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<h4 class="modal-title" id="myModalLabel">Reset Your Password</h4>
						  </div>
						  <div class="modal-body">
						  	<div id="pwResetError"></div>
							<form class="forgotPWForm">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="email">Enter Your Email Address</label>
											<input type="email" name="email" class="form-control input-md" required>
										</div>
									</div>
								</div>
								<input id="new-class-btn" type="submit" class="btn btn-primary" value="Submit Reset">
							</form>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  </div>
						</div>
					  </div>
					</div>
				';
			}
		}
		
		
		/*
		*	Get the modal
		*/
		public function getModal()
		{
			return $this->modalBuilt;
		}
	}
?>