<?php

/**
 * Provides useful functions for i/o related actions.
 */
abstract class IO_Utils {
	/**
	 * Returns the given path relative to document root.
	 */
	public static function getRelativePath($path) {
		$documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
		$path = str_replace('\\', '/', realpath($path));
		return trim(str_replace($documentRoot, '', $path), '/');
	}
	
	/**
	 * Loads all files from the given folder.
	 * @return an array containing the names of all files in the given folder
	 */
	public static function getFilesFromFolder($path) {
		$files = array();
		if($handle = opendir($path)) {
			while(false !== ($fileName = readdir($handle))) {
				if($fileName != "." && $fileName != "..") {
					$files[] = $fileName;
				}
			}
			closedir($handle);
		}
		
		return $files;
	}
}

?>