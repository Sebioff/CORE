<?php

/**
 * Handles execution of migration files located under PROJECT_ROOT/migrations
 * A migration is a file containing a set of database instructions (usually database
 * structure + content)
 */
class Core_MigrationsLoader {
	// path of migration logfile, relative to PROJECT_PATH
	const MIGRATION_LOG_FILE = '/config/log/migrations.log.xml';
	
	private static $migrationFolders = array();
	
	/**
	 * Loads all migration files from the migration folder (in alphabetical order)
	 * and executes the contained mysql queries.
	 * Every migration is executed only once.
	 */
	public static function load() {
		// add project migration folder
		self::addMigrationFolder(PROJECT_PATH.'/migrations');
		
		// load migration logfile or create new one
		if (file_exists(PROJECT_PATH.self::MIGRATION_LOG_FILE))
			$xml = simplexml_load_file(PROJECT_PATH.self::MIGRATION_LOG_FILE);
		else {
			$baseMigrationLog = '<?xml version=\'1.0\'?><content></content>';
			$xml = new SimpleXMLElement($baseMigrationLog);
		}
		
		foreach (self::$migrationFolders as $migrationFolderArray) {
			$relativeMigrationFolder = strtolower(IO_Utils::getRelativePath($migrationFolderArray['path'], CORE_PATH.'/..'));
			$fileXPathParts = explode('/', $relativeMigrationFolder);
			
			// create xpath to current migration folder if it doesn't exist
			if (!count($xml->xpath('/content/'.$relativeMigrationFolder))) {
				$currentXMLNode = $xml;
				$fileXPathPartsSize = count($fileXPathParts);
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
			$migrationFiles = IO_Utils::getFilesFromFolder($migrationFolderArray['path'], array('php'));
			natsort($migrationFiles);
			foreach ($migrationFiles as $migrationFile) {
				$result = $xml->xpath(sprintf('/content/%s/file[@name=\'%s\']', implode('/', $fileXPathParts), $migrationFile));
				if (empty($result)) {
					$result = $xml->xpath(sprintf('/content/%s', implode('/', $fileXPathParts)));
					$child = $result[0]->addChild('file');
					$child->addAttribute('name', $migrationFile);
					self::executeMigration($migrationFolderArray['path'].'/'.$migrationFile, $migrationFolderArray['vars'], $migrationFolderArray['connection']);
				}
			}
		}
		
		// save migration logfile
		$doc = new DOMDocument('1.0');
		$doc->preserveWhiteSpace = false;
		$doc->loadXML($xml->asXML());
		$doc->formatOutput = true;
		$doc->save(PROJECT_PATH.self::MIGRATION_LOG_FILE);
	}
	
	/**
	 * Executes all sql queries in the given migration file
	 * @param $var_array array associative array of key/values that are available
	 * for use in the migration file. E.g.: array('key' = 'value') can be used
	 * as $key in the migration file.
	 */
	public static function executeMigration($migrationFile, array $var_array = array(), DB_Connection $connection = null) {
		extract($var_array);
		$queries = array();
		require $migrationFile;
		if (!$connection)
			$connection = DB_Connection::get();
		$connection->beginTransaction();
		foreach ($queries as $query)
			$connection->query($query);
		$connection->commit();
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public static function addMigrationFolder($migrationFolderPath, array $var_array = array(), DB_Connection $connection = null) {
		self::$migrationFolders[] = array(
			'path' => $migrationFolderPath,
			'vars' => $var_array,
			'connection' => $connection
		);
	}
}

?>