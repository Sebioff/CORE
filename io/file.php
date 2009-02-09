<?php

/**
 * Provides functions to ease working with files
 */
abstract class IO_File {
	public function __construct($filePath) {
		$this->file=$filePath;
		$this->exists=file_exists($filePath);
	}
	
	private $file;
	private $exists;
	private $resource;
	
	public function exists() 																											{return file_exists($this->file);}
	public function isReadable() 																									{return is_readable($this->file);}
	
	/**
	 * opens the file
	 * if the file doesn't exist it will be created
	 */
	public function open($mode='a') {
		$this->resource=fopen($this->file, $mode);
	}
	
	public function delete() {
		if($this->exists()) {
			if(!unlink($this->file))
				throw new Core_Exception('Unable to delete File please check permissions!');
			else
				return true;
		}
		else
			return true;
	}
}

?>