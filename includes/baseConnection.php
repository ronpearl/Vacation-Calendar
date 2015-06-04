<?php
	/*
	*  Base connection class to run the DB connections of the website.
	*  
	*/
	class baseConnection
	{
		protected $serverName;
		protected $serverUsername;
		protected $serverPW;
		protected $dbName;
		
		protected $conn;
		
		public function __construct() /*server, username, password, dbName*/
		{
			$this->serverName = 'server';
			$this->serverUsername = 'user';
			$this->serverPW = 'pass';
			$this->dbName = 'db';
			
			$this->doConnect();
		}
		
		/*
		*	Connect using PDO
		*/
		public function doConnect()
		{
			try {
				$this->conn = new PDO("mysql:host=$this->serverName;dbname=$this->dbName", $this->serverUsername, $this->serverPW);
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
		
		
		/*
		*	Get the PDO connection object
		*/
		public function getConn()
		{
			return $this->conn;
		}
	}
?>