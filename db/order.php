<?php
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