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
 * Ensures the control contains a valid mail address.
 */
class GUI_Validator_Mail extends GUI_Validator {
	// OVERRIDES / IMPLEMENTS --------------------------------------------------
	public function isValid() {
		$nonascii      = "\x80-\xff"; // Non-ASCII-Chars are not allowed
	    $nqtext        = "[^\\\\$nonascii\015\012\"]";
	    $qchar         = "\\\\[^$nonascii]";
	    $protocol      = '(?:mailto:)';
	    $normuser      = '[a-zA-Z0-9-][a-zA-Z0-9_.-]*';
	    $quotedstring  = "\"(?:$nqtext|$qchar)+\"";
	    $user_part     = "(?:$normuser|$quotedstring)";
	    $dom_mainpart  = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
	    $dom_subpart   = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
	    $dom_tldpart   = '[a-zA-Z]{2,5}';
	    $domain_part   = "$dom_subpart$dom_mainpart$dom_tldpart";
	    $regex         = "$protocol?$user_part\@$domain_part";
		
		return (bool)preg_match("/^$regex$/", $this->control->getValue());
	}
	
	public function getError() {
		return 'Keine gültige EMail-Adresse';
	}
	
	public function getJs() {
		return array('email', 'true');
	}
}

?>