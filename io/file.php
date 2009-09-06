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
	
	public function delete() {
		if (!$this->exists())
			return true;
		return unlink($this->file);
	}
	
	/**
	 * Reads an amount of bytes from the file, specified by $length
	 * @param $length the amount of bytes to read (all, if not given)
	 * @return the amount of read bytes
	 */
	public function read($length = null) {
		if($length)
			$length = filesize($this->file);
			
		$result = fread($this->resource, filesize($this->file));
		
		if(!$result)
			throw new Core_Exception('Can\' read file (mode is '.$this->mode.').');
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
		if(!$result)
			throw new Core_Exception('Can\' write file (mode is '.$this->mode.').');
		else
			return $result;
	}
}

?>