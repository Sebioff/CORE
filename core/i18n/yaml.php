<?php

/**
 * instance of an yaml object
 */
class I18N_Yaml {
	/** allowed File types for parsing */
	private $allowedFileExtensions = array('yaml', 'yml');
	
	// CUSTOM METHODS ----------------------------------------------------------
	/**
	 * return generated array of an .yml file
	 */
	private function parseFile($file) {
		return I18N_Spyc::YAMLLoad($file);
	}
	 
	/**
	 * load translations from directory _not_ recursively
	 */
	public function loadFilesFromFolder($folder) {
		$translations=array();
		foreach(IO_Utils::getFilesFromFolder($folder, $this->allowedFileExtensions) as $file) {
			$filename='';
			foreach($this->allowedFileExtensions as $extension) {
				if(!$filename || Text::length(basename($file, '.'.$extension)) < Text::length($filename))
					$filename=basename($file, '.'.$extension);
			}
			$translations[$filename]=I18N_Spyc::YAMLLoad($folder.'/'.$file);
		}
		return $translations;
	}
}

?>