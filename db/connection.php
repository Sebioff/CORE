<?php
  // Interfaces:
  // Core_DB_Connection

  //===========================================================================
  // Core_DB_Connection:
  //===========================================================================
  class Core_DB_Connection
  {
    // construction
    public function __construct($server_, $database_, $user_, $password_=null)
  	{
      $this->user=$user_;
      $this->password=$password_;
      $this->database=$database_;
      $this->link=mysql_connect($server_, $user_, $password_);
      mysql_select_db($database_, $this->link);
  	}
    
    // Class Variables
    public $link=null;
    private $user='';
    private $password='';
    public $database='';
    // ----
  }
  //===========================================================================
?>