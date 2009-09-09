<?php

/**
 * Implement this interface if you want to restrict access to your scriptlets.
 */
interface Scriptlet_Privileged {
	/**
	 * @return boolean true if acccess is granted, false otherwhise
	 */
	public function checkPrivileges();
}

?>