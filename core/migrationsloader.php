<?php

/**
 * Handles execution of migration files located under [PROJECT_ROOT]/migrations.
 */
class Core_MigrationsLoader {
	const MIGRATION_LOG_FILE = '../config/log/migrations.log.xml';
	
	public static function reset() {
		$con=DB_Connection::get();
	
		$tables = $con->query("SHOW TABLES");
		while ($table = mysql_fetch_row($tables)) {
			$query="DROP TABLE `".$table[0]."` ; ";
			$con->query($query);
		}

		$file=new IO_File(self::MIGRATION_LOG_FILE);
		if($file->exists())
			$file->delete();
		
		$url=sprintf('http://%s', $_SERVER['SERVER_NAME']);
		Scriptlet::redirect($url);
	}
	
	/**
	 * Loads all migration files from the migration folder (in the order they where
	 * created) and executes the contained mysql queries.
	 * Every migration is executed only once.
	 */
	public static function load() {
		$migrationFolder = '../migrations';
		$relativeMigrationFolder = strtolower(IO_Utils::getRelativePath($migrationFolder));
		$fileXPathParts = explode('/', $relativeMigrationFolder);
		
		// load migration logfile or create new one
		if(file_exists(self::MIGRATION_LOG_FILE))
			$xml = simplexml_load_file(self::MIGRATION_LOG_FILE);
		else {
			$baseMigrationLog = <<<XML
<?xml version='1.0'?>
<content>
</content>
XML;
			$xml = new SimpleXMLElement($baseMigrationLog);
		}
		
		// create xpath to current migration folder if it doesn't exist
		if (!count($xml->xpath('/content/'.$relativeMigrationFolder))) {
			$currentXMLNode = $xml;
			$fileXPathPartsSize=count($fileXPathParts);
			for($i = 0; $i < $fileXPathPartsSize; $i++) {
				$fileXPath = array_slice($fileXPathParts, 0, $i+1);
				$fileXPath = implode('/', $fileXPath);
				$result = $xml->xpath('/content/'.$fileXPath);
				if(!count($result))
					$currentXMLNode = $currentXMLNode[0]->addChild($fileXPathParts[$i]);
				else
					$currentXMLNode = $result;
			}
		}
		
		// execute migration files if they haven't been before
		$migrationFiles = IO_Utils::getFilesFromFolder($migrationFolder, array('php'));
		foreach($migrationFiles as $migrationFile) {
			$result = $xml->xpath(sprintf('/content/%s/file[@name=\'%s\']', implode('/', $fileXPathParts), $migrationFile));
			if(!count($result)) {
				$result = $xml->xpath(sprintf('/content/%s', implode('/', $fileXPathParts)));
				$child = $result[0]->addChild('file');
				$child->addAttribute('name', $migrationFile);
				self::executeMigration($migrationFolder.'/'.$migrationFile);
			}
		}
		
		// save migration logfile
		$doc = new DOMDocument('1.0');
		$doc->preserveWhiteSpace = false;
		$doc->loadXML($xml->asXML());
		$doc->formatOutput = true;
		$doc->save(self::MIGRATION_LOG_FILE);
	}
	
	/**
	 * Executes all sql queries in the given migration file
	 */
	private static function executeMigration($migrationFile) {
		$queries = array();
		require_once $migrationFile;
		foreach($queries as $query)
			DB_Connection::get()->query($query);
	}
}

?>