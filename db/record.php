<?php
  // Interfaces:
  // Core_DB_Record

  //===========================================================================
  // Core_DB_Record:
  //===========================================================================
  class Core_DB_Record
  {
    // construction
    function __construct(DB_Container $container_)
    {
      $this->m_container=$container_;
    }
    
    // Class Variables
    private $_vars=array();
    private $m_container=null;
    // ----

    // Setter/Getter
    public function __get($var_)
    {
      if(isset($this->_vars[$var_]))
        return $this->_vars[$var_];
      elseif(isset($this->{'m_'.$var_}))
        return $this->{'m_'.$var_};
    	else
        return null;
    }
    
    public function __set($var_, $value_)                                       {$this->_vars[$var_]=$value_;}
    
    public function get()                                                       {return $this->_vars;}
  }
  //===========================================================================
?>