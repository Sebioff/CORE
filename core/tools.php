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
 * provides some Tools
 */
abstract class Tools {
	/**
	 * Casts a given Object to another
	 * @param $object The object to cast
	 * @param $newClass Name or object to cast to
	 * @return $object of new Type
	 */
	public static function classCast($object, $newClass) {
		if (is_object($newClass))
			$newClass = get_class($newClass);
		if (!class_exists($newClass)) {
			throw new Core_Exception('Class '.$newClass.' not found');
			return null;
		}
		if (get_parent_class($newClass) != get_class($object) && !is_subclass_of($object, $newclass)) {
			throw new Core_Exception('Given Object is no child or parent of '.$newClass);
			return null;
		}
    	$old_serialized_object = serialize($object);
    	$new_serialized_object = 'O:'.strlen($newClass).':"'.$newClass.'":'.
    		substr($old_serialized_object, $old_serialized_object[2] + 7);
    	return unserialize($new_serialized_object);
	}
}
?>