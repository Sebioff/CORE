<?php

/**
 * Provides useful functions for i/o related actions.
 */
abstract class IO_Utils {
	/**
	 * Returns the given path relative to a given parent folder.
	 */
	public static function getRelativePath($path, $parentFolder) {
		$documentRoot = str_replace('\\', '/', realpath($parentFolder));
		$path = str_replace('\\', '/', realpath($path));
		return trim(str_replace($documentRoot, '', $path), '/');
	}
	
	/**
	 * Loads all files from the given folder.
	 * @param $extensions array if set, only files with extensions defined in this array
	 * are returned
	 * @return an array containing the names of all files in the given folder
	 */
	public static function getFilesFromFolder($path, Array $extensions = null) {
		$files = array();
		if ($handle = opendir($path)) {
			while (false !== ($fileName = readdir($handle))) {
				if ($fileName != '.' && $fileName != '..') {
					if (!$extensions || in_array(self::getFileExtension($fileName), $extensions))
						$files[] = $fileName;
				}
			}
			closedir($handle);
		}
		
		return $files;
	}
	
	// TODO add method for getting files recursively from folder
	
	/**
	* returns extension of file
	*/
	public static function getFileExtension($path) {
		return pathinfo($path, PATHINFO_EXTENSION);
	}
}

?>