<?php

/**
 * Provides functions to ease working with files
 */
class IO_File {
	const READ_PREPEND = 'r';
	const READ_WRITE_PREPEND = 'r+';
	const WRITE_NEWFILE = 'w';
	const READ_WRITE_NEWFILE = 'w+';
	const WRITE_APPEND = 'a';
	const READ_WRITE_APPEND = 'a+';
	
	private $file;
	private $resource;
	private $mode;
	
	public function __construct($filePath) {
		$this->file = $filePath;
	}
	
	public function exists() {
		return file_exists($this->file);
	
	}
	public function isReadable() {
		return is_readable($this->file);
	}
	
	/**
	 * opens the file
	 */
	public function open($mode = self::READ_WRITE_PREPEND) {
		$this->mode = $mode;
		$this->resource = fopen($this->file, $mode);
	}
	
	public function close() {
		return fclose($this->resource);
	}
	
	public function create() {
		if ($this->exists())
			return false;
		return touch($this->file);
	}
	
	public function delete() {
		if (!$this->exists())
			return false;
		return unlink($this->file);
	}
	
	/**
	 * Reads an amount of bytes from the file, specified by $length
	 * @param $length the amount of bytes to read (all, if not given)
	 * @return string the read bytes
	 */
	public function read($length = null) {
		if ($length === null)
			$length = filesize($this->file);
			
		$result = fread($this->resource, $length);
		
		if ($result === false)
			throw new Core_Exception('Can\'t read file (mode is '.$this->mode.').');
		else
			return $result;
	}
	
	/**
	 * Writes into the file.
	 * @param $string the string to write
	 * @return the number of written bytes
	 */
	public function write($string) {
		$result = fwrite($this->resource, $string);
		if ($result === false)
			throw new Core_Exception('Can\'t write file (mode is '.$this->mode.').');
		else
			return $result;
	}
	
	/**
	 * @return int the unix timestamp at which this file has been modified last
	 */
	public function getLastModifiedTime() {
		return filemtime($this->file);
	}
}

?>