<?php

/**
 * @package CORE PHP Framework
 * @copyright Copyright (C) 2012 Sebastian Mayer, Andreas Sicking, Andre Jährling
 * @license GNU/GPL, see license.txt
 * This file is part of CORE PHP Framework.
 *
 * CORE PHP Framework is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CORE PHP Framework is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CORE PHP Framework. If not, see <http://www.gnu.org/licenses/>.
 */

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
	 * @throws Core_Exception if the file can't be read
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
	 * @throws Core_Exception if the file can't be written
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