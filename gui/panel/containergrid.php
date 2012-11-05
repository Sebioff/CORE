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
 * Displays content of a DB_Container in a table.
 * Remember that you can use filtered containers to limit which records will be
 * displayed in this panel.
 * TODO add CRUD functionality
 * @deprecated not really deprecated, just unfinished. TODO finish.
 */
class GUI_Panel_ContainerGrid extends GUI_Panel_Table {
	private $container = null;
	private $visibleColumns = array();
	private $columnTitles = array();
	
	public function __construct($name, DB_Container $container, array $visibleColumns = array(), array $columnTitles = array(), $title = '') {
		parent::__construct($name, $title);
		
		$this->container = $container;
		$this->visibleColumns = $visibleColumns;
		$this->columnTitles = $columnTitles;
	}
	
	public function init() {
		parent::init();
		
		$columns = $this->visibleColumns;
		if (!empty($columns)) {
			$columnCount = count($columns);
			for ($i = 0; $i < $columnCount; $i++) {
				$columns[$i] = Text::underscoreToCamelCase($columns[i]);
			}
		}
		
		$records = $this->container->select();
//		if (empty($columns) && !empty($records))
//			$columns = $records[0]->getAllProperties();
			
		$titles = $this->columnTitles;
		if (empty($titles))
			$titles = $columns;
			
		$this->addHeader($titles);
		foreach ($records as $record) {
			
		}
	}
}

?>