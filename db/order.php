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
 * Orders an Array of DB_Records by another Array of DB_Records.
 * @param records: Array of DB_Records to sort
 * @param orders: Array of DB_Records to sort with
 */
class DB_Order {
	private $records = array();
	private $orders = array();
	private $foreignKey = null;
	
	public function __construct(array $records, array $orders) {
		$PKs = array();
		//Save every DB_Record from $records and its PK
		foreach ($records as $record) {
			if ($record instanceof DB_Record)
				$this->records[] = $record;
				$PKs[] = $record->getPK();
		}
		//try to get the connection from records to orders (pk -> foreign key)
		$databaseSchema = $orders[0]->getContainer()->getDatabaseSchema();
		$tablename = $this->records[0]->getContainer()->getTable();
		foreach ($databaseSchema['constraints'] as $key => $constraint) {
			if ($constraint['referencedTable'] == $tablename)
				$this->foreignKey = $key;
		}
		//save every DB_Record from $orders
		if ($this->foreignKey) {
			foreach ($orders as $order) {
				if ($order instanceof DB_Record && in_array($order->{$this->foreignKey}->id, $PKs) && !in_array($order->{$this->foreignKey}->id, $this->orders)) {
					$this->orders[] = $order->{$this->foreignKey}->id;
				}
			}
		}
	}
	public function asc() {
		if (count($this->records) == 0 || count($this->orders) == 0)
			return null;
		//Bubblesort
		$n = count($this->records);
		for ($i = 0; $i < $n; $i++) {
			for ($j = $i; $j < $n; $j++) {
				$keyA = -1;
				$keyB = -1;
				for ($k = 0; $k < count($this->orders); $k++) {
					if ($this->orders[$k] == $this->records[$i]->getPK())
						$keyA = $k;
					if ($this->orders[$k] == $this->records[$j]->getPK())
						$keyB = $k;
				}
				if ($keyA < $keyB) {
					$_tmp = $this->records[$j];
					$this->records[$j] = $this->records[$i];
					$this->records[$i] = $_tmp;
				}
			}
		}
		return $this->records;
	}
	public function desc() {
		if (count($this->records) == 0 || count($this->orders) == 0)
			return null;
		//Bubblesort
		$n = count($this->records);
		for ($i = 0; $i < $n; $i++) {
			for ($j = $i; $j < $n; $j++) {
				$keyA = $n;
				$keyB = $n;
				for ($k = 0; $k < count($this->orders); $k++) {
					if ($this->orders[$k] == $this->records[$i]->getPK())
						$keyA = $k;
					if ($this->orders[$k] == $this->records[$j]->getPK())
						$keyB = $k;
				}
				if ($keyA > $keyB) {
					$_tmp = $this->records[$j];
					$this->records[$j] = $this->records[$i];
					$this->records[$i] = $_tmp;
				}
			}
		}
		return $this->records;
	}
}
?>