<?php

/**
 * Handles execution of migration files located under PROJECT_ROOT/migrations
 * A migration is a file containing a set of database instructions (usually database structure
 * + content)
 */
class Core_MigrationsLoader {
	const MIGRATION_LOG_FILE = '../config/log/migrations.log.xml';
	
	/**
	 * Loads all migration files from the migration folder (in the order they where
	 * created) and executes the contained mysql queries.
	 * Every migration is executed only once.
	 */
	public static function load() {
		$migrationFolder = PROJECT_PATH.'/migrations';
		$relativeMigrationFolder = strtolower(IO_Utils::getRelativePath($migrationFolder, PROJECT_PATH.'/..'));
		$fileXPathParts = explode('/', $relativeMigrationFolder);
		
		// load migration logfile or create new one
		if (file_exists(self::MIGRATION_LOG_FILE))
			$xml = simplexml_load_file(self::MIGRATION_LOG_FILE);
		else {
			$baseMigrationLog = '<?xml version=\'1.0\'?><content></content>';
			$xml = new SimpleXMLElement($baseMigrationLog);
		}
		
		// create xpath to current migration folder if it doesn't exist
		if (!count($xml->xpath('/content/'.$relativeMigrationFolder))) {
			$currentXMLNode = $xml;
			$fileXPathPartsSize=count($fileXPathParts);
			for ($i = 0; $i < $fileXPathPartsSize; $i++) {
				$fileXPath = array_slice($fileXPathParts, 0, $i+1);
				$fileXPath = implode('/', $fileXPath);
				$result = $xml->xpath('/content/'.$fileXPath);
				if (empty($result))
					$currentXMLNode = $currentXMLNode[0]->addChild($fileXPathParts[$i]);
				else
					$currentXMLNode = $result;
			}
		}
		
		// execute migration files if they haven't been before
		$migrationFiles = IO_Utils::getFilesFromFolder($migrationFolder, array('php'));
		natsort($migrationFiles);
		foreach ($migrationFiles as $migrationFile) {
			$result = $xml->xpath(sprintf('/content/%s/file[@name=\'%s\']', implode('/', $fileXPathParts), $migrationFile));
			if (empty($result)) {
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
	public static function executeMigration($migrationFile, array $var_array = array()) {
		extract($var_array);
		$queries = array();
		require $migrationFile;
		foreach ($queries as $query)
			DB_Connection::get()->query($query);
	}
}

?>