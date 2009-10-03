<?php

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