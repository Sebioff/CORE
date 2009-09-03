<?php
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