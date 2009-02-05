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
      $this->m_user=$user_;
      $this->m_password=$password_;
      $this->m_database=$database_;
      $this->m_link=mysql_connect($server_, $user_, $password_);
      if(!mysql_select_db($database_, $this->m_link))
      {
        $query='CREATE DATABASE '.$database_;
        mysql_query($query, $this->m_link);
        mysql_select_db($database_, $this->m_link);
      }
  	}
    
    // Class Variables
    private $m_link=null;
    private $m_user='';
    private $m_password='';
    private $m_database='';
    // ----

    // Setter/Getter
    public function __get($param)
    {
      return isset($this->{'m_'.$param})?$this->{'m_'.$param}:null;
    }
  }
  //===========================================================================
?>