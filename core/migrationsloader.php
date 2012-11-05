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
		// load migration logfile or create new one
		if (file_exists(PROJECT_PATH.self::MIGRATION_LOG_FILE))
			$xml = simplexml_load_file(PROJECT_PATH.self::MIGRATION_LOG_FILE);
		else {
			$baseMigrationLog = '<?xml version=\'1.0\'?><content></content>';
			$xml = new SimpleXMLElement($baseMigrationLog);
		}
		
		foreach (self::$migrationFolders as $migrationFolderArray) {
			$relativeMigrationFolder = strtolower(IO_Utils::getRelativePath($migrationFolderArray['path'], CORE_PATH.'/..'));
			$folderParts = explode('/', $relativeMigrationFolder);
			$folderXPathParts = $folderParts;
			$folderXPathPartsSize = count($folderXPathParts);
			for ($i = 0; $i < $folderXPathPartsSize; $i++)
				$folderXPathParts[$i] = 'folder[@name=\''.$folderXPathParts[$i].'\']';
			$folderXPath = '/content/'.implode('/', $folderXPathParts);
			
			// create xpath to current migration folder if it doesn't exist
			if (!$xml->xpath($folderXPath)) {
				$currentXMLNode = $xml;
				for ($i = 0; $i < $folderXPathPartsSize; $i++) {
					$partialFolderXPath = array_slice($folderXPathParts, 0, $i + 1);
					$result = $xml->xpath('/content/'.implode('/', $partialFolderXPath));
					if (empty($result)) {
						$currentXMLNode = $currentXMLNode[0]->addChild('folder');
						$currentXMLNode->addAttribute('name', $folderParts[$i]);
					}
					else {
						$currentXMLNode = $result;
					}
				}
			}
			
			// execute migration files if they haven't been before
			$migrationFiles = IO_Utils::getFilesFromFolder($migrationFolderArray['path'], array('php'));
			natsort($migrationFiles);
			foreach ($migrationFiles as $migrationFile) {
				$result = $xml->xpath(sprintf($folderXPath.'/file[@name=\'%s\']', $migrationFile));
				if (empty($result)) {
					$result = $xml->xpath($folderXPath);
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