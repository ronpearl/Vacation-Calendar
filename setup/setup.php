<?php
	/**
		SETUP DATABASE FILE
		
		NOTICE!!!!!
		------------
		This file is not fully functioning or tested. 
	**/

	class setupDB extends baseConnection
	{
		protected $weeksInAYear = 52;
		protected $dbConnection;
		
		public function __construct()
		{
			// Start DB Connection
			$this->dbConnection = new baseConnection;
		}
		
		
		private function createOncallTable()
		{
			$conn = $this->dbConnection->getConn();
			$query = $conn->prepare("CREATE TABLE vacations_oncall (
				week_number TINYINT(2) UNSIGNED PRIMARY KEY,
				user INT(6) UNSIGNED NOT NULL
			)");
			$query->execute();
			
			// Now add the weeks to the table
			for ($i = 1; $i <= $this->weeksInAYear; $i++)
			{
				$conn = $this->dbConnection->getConn();
				$query = $conn->prepare("INSERT INTO vacations_oncall (week_number) VALUES ('$i')");
				$query->execute();
			}
		}
		
		
		public function addDataToOncall($userIDsToAdd)
		{
			$numUsersMinusOne = count($userIDsToAdd)-1;
			
			for ($i = 1; $i <= $this->weeksInAYear; $i++)
			{
				$startingIndex = $i-1;
				
				$conn = $this->dbConnection->getConn();
				$query = $conn->prepare("UPDATE vacations_oncall SET user = :user WHERE week_number = '$i'");
				$query->execute(array(
					':user' => $userIDsToAdd[$startingIndex]
				));
				
				$firstValueOfArray = $userIDsToAdd[$startingIndex];
				unset($userIDsToAdd[$startingIndex]);
				$incrementing = $i+$numUsersMinusOne;
				$userIDsToAdd[$incrementing] = $firstValueOfArray;
			}
		}
	}
?>