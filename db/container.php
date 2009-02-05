<?php
  // Interfaces:
  // Core_DB_Container
  // Core_DB_Container_Interface

  //===========================================================================
  // Core_DB_Container:
  //===========================================================================
  class Core_DB_Container implements Core_DB_Container_Interface
  {
    // construction
    public function __construct($table_, DB_Connection $con_=null, $recordClass_='DB_Record')
  	{
  	  if(!$con_)
  	   $con_=App::current()->getConnection();
  	  $this->table=$table_;
  	  $this->con=$con_;
  	  $this->recordClass=$recordClass_;
  	  
  	  $query=sprintf("CREATE TABLE IF NOT EXISTS `%s`.`%s` (
                      `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY
                      ) ENGINE = MYISAM ",
  	                  $this->con->database, $this->table);
  	  $this->query($query);
  	  $this->columns=$this->init();
  	  $columns=$this->getColumns();
  	  $columnNames=array();
  	  foreach($columns as $column)
  	    $columnNames[]=$column->Field;
  	  foreach($this->columns as $column)
  	  {
  	    if(!in_array($column[0], $columnNames))
  	    {
  	      if(in_array($column[1], $this->allowedColumnTypes))
  	      {
  	        $suffix='';
  	        if($column[1]=='VARCHAR')
  	          $suffix='( 255 )';
  	        
  	        $query=sprintf("ALTER TABLE `%s`.`%s`
  	                        ADD `%s` %s%s NOT NULL ;",
  	                        $this->con->database, $this->table, $column[0], $column[1], $suffix);
            $this->query($query);
  	      }
  	      else
  	        App::current()->exception(App::WRONG_ARGUMENT_TYPE);
  	    }
  	  }
  	}
  	
  	public function query($query_)
  	{
  	  $result=@mysql_query($query_, $this->con->link) or die(mysql_error());
  	  return $result;
  	}
  	public function getColumns()
  	{
      $query=sprintf("SHOW COLUMNS FROM %s",
                     $this->table);
      $result=$this->query($query);
      while($recordContents=mysql_fetch_array($result, MYSQL_ASSOC))
      {
        $record=new $this->recordClass($this);
        foreach($recordContents as $key=>$content)
        {
          $record->{$key}=$content;
        }
        $records[]=$record;
      }
      mysql_free_result($result);
      return $records;
  	}
  	public function findAll($filter_=null, $limit_=null)
  	{
  	  $query=sprintf("SELECT * FROM `%s`.`%s` ",
  	                 $this->con->database, $this->table);
      if($filter_)
        $query.=sprintf("WHERE %s ", $filter_);
      $query.='ORDER BY `id` ASC ';
      if($limit_)
        $query.=sprintf("LIMIT 0, %s ", $limit_);
  	  $result=$this->query($query, $this->con->link);
  	  $records=array();
  	  while($recordContents=mysql_fetch_array($result, MYSQL_ASSOC))
  	  {
        $record=new $this->recordClass($this);
  	    foreach($recordContents as $key=>$content)
  	    {
  	      $record->{$key}=$content;
  	    }
  	    $records[]=$record;
  	  }
  	  mysql_free_result($result);
  	  return $records;
  	}
  	public function findFirst($filter_)
  	{
  	  $record=$this->findAll($filter_, 1);
  	  return $record[0];
  	}
  	public function insert(DB_Record $record_)
  	{
  	  $columns=array();
  	  $i=0;
  	  foreach($this->getColumns() as $column)
  	  {
        $columns['column'][$i]=$column->Field;
        $columns['default'][$i]=$column->Default;
        if(!strlen($columns['default'][$i]))
          $columns['default'][$i]='NULL';
        $i++;
  	  }
      $i=0;
      foreach($record_->get() as $columnName=>$value)
      {
        foreach($columns['column'] as $part=>$column)
        {
          if($columnName==$column)
            $columns['default'][$part]=sprintf("'%s'", $value);
        }
      }
  	  $query=sprintf("INSERT INTO `%s`.`%s`(%s) VALUES (%s)",
  	                 $this->con->database, $this->table,
  	                 implode(', ', $columns['column']),
  	                 implode(', ', $columns['default']));
      $this->query($query);
  	}
  	public function save(DB_Record $record_)
  	{
  	  if(!$record_->id)
  	    $this->insert($record_);
  	  else
  	  {
  	    $contents=$record_->get();
  	    $setter=array();
  	    foreach($contents as $column=>$value)
  	    {
  	      $setter[]=sprintf("%s = '%s'", $column, $value);
  	    }
  	    $query=sprintf("UPDATE `%s`.`%s` SET %s WHERE `%s`.`id`= '%s' LIMIT 1",
  	                   $this->con->database, $this->table,
  	                   implode(', ', $setter), $this->table, $record_->id);
        $this->query($query);
  	  }
  	}
  	
  	/*private*/ function init()
  	{
  	  $columns=array();
  	  return $columns;
  	}
    
    // Class Variables
    private $m_table=null;
    private $m_con=null;
    private $m_recordClass=null;
    private $m_columns=array();
    private $m_allowedColumnTypes=array('INT', 'VARCHAR', 'TIMESTAMP');
    // ----

    // Setter/Getter
    public function __get($param)
    {
      return isset($this->{'m_'.$param})?$this->{'m_'.$param}:null;
    }
  }
  //===========================================================================

  
  //===========================================================================
  // Core_DB_Container_Interface:
  //===========================================================================
  interface DB_Container_Interface
  {
    /*private*/ function init();
  }
  //===========================================================================
?>