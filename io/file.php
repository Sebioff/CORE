<?php

/**
 * Provides functions to ease working with files
 */
class IO_File {
	public function __construct($filePath) {
		$this->file=$filePath;
		$this->exists=file_exists($filePath);
	}
	
	private $file;
	private $exists;
	private $resource;
	private $mode;
	
	public function exists() {
		return file_exists($this->file);
	
	}
	public function isReadable() {
		return is_readable($this->file);
	}
	
	/**
	 * opens the file
	 * if the file doesn't exist it will be created
	 */
	public function open($mode='r+') {
		$this->mode=$mode;
		$this->resource=fopen($this->file, $mode);
	}
	
	public function close() {
		return fclose($this->resource);
	}
	
	public function delete() {
		return unlink($this->file);
	}
	
	public function append($string) {
		if($this->mode!='a' && $this->mode!='a+') {
			$this->close();
			$this->open('a');
		}
		fwrite($this->resource, $string);
	}
	
	public function read() {
		if($this->mode!='r+') {
			$this->close();
			$this->open('r+');
		}
		if(filesize($this->file)) {
			return fread($this->resource, filesize($this->file));
		}
		else
			return false;
	}
	
	public function write($string) {
		fwrite($this->resource, $string);
	}
}

?>