<?
  class I18N_Yaml {
 
  	/** instance of an yaml object */
  	
  	/** allowed File types for parsing */
  	private $allowedFileExtensions = array('yaml', 'yml');
  	
  	/** return generated array of an .yml file */
  	private function parseFile($file) {
  		return I18N_Spyc::YAMLLoad($file);
  	}
  	
  	/** load translations from directory _not_ recursively_ */
  	public function loadFilesFromFolder($folder) {
  		$translations=array();
  		foreach(IO_Utils::getFilesFromFolder($folder, $this->allowedFileExtensions) as $file) {
  			$filename='';
  			foreach($this->allowedFileExtensions as $extension) {
  				if($filename=='' || mb_strlen(basename($file, '.'.$extension)) < mb_strlen($filename))
  				  $filename=basename($file, '.'.$extension);
  			}
  		  $translations[$filename]=I18N_Spyc::YAMLLoad($folder.'/'.$file);
  		}
  		return $translations;
  	}
  }
?>