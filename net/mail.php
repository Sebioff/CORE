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
 * Allows easy, OOP-style sending of mails
 * Definable constants:
 * - CORE_MAILSENDER (standard sender if none is set)
 */
class Net_Mail {
	private $recipients = array();
	private $cc = array();
	private $bcc = array();
	private $sender = '';
	private $subject = '';
	private $message = '';
	
	public function __construct() {
		if (defined('CORE_MAILSENDER'))
			$this->setSender(CORE_MAILSENDER);
	}
	
	/**
	 * Adds one or more recipients to this mail.
	 * @param $recipients array or list of recipients
	 */
	public function addRecipients($recipients) {
		if (is_array($recipients))
			$this->recipients = array_merge($this->recipients, $recipients);
		else
			foreach (func_get_args() as $recipient)
				$this->recipients[] = $recipient;
	}
	
	public function addRecipient($mailaddress, $name = '') {
		if ($name)
			$mailaddress = $name.' <'.$mailaddress.'>';
		$this->recipients[] = $mailaddress;
	}
	
	/**
	 * Adds one or more copy recipients to this mail.
	 * @param $recipients array or list of recipients
	 */
	public function addCC($ccs) {
		if (is_array($ccs))
			$this->recipients = array_merge($this->cc, $ccs);
		else
			foreach (func_get_args() as $cc)
				$this->cc[] = $cc;
	}
	
	/**
	 * Adds one or more blind copy recipients to this mail.
	 * @param $recipients array or list of recipients
	 */
	public function addBCC($bccs) {
		if (is_array($bccs))
			$this->bcc = array_merge($this->bcc, $bccs);
		else
			foreach (func_get_args() as $bcc)
				$this->bcc[] = $bcc;
	}
	
	/**
	 * Sends the mail.
	 */
	public function send() {
		$headers = 'X-Mailer: CORE PHP Framework'."\r\n";
		$headers .= 'Content-type: text/plain; charset=UTF-8'."\r\n";
		if ($this->sender)
			$headers .= 'From: '.$this->sender."\r\n";
		if (!empty($this->cc))
			$headers .= 'Cc: '.implode(', ', $this->cc)."\r\n";
		if (!empty($this->bcc))
			$headers .= 'Bcc: '.implode(', ', $this->bcc)."\r\n";
		
		mail(implode(', ', $this->recipients), $this->subject, $this->message, $headers);
	}
	
	// GETTERS / SETTERS -------------------------------------------------------
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	public function setSender($sender, $name = '') {
		if ($name)
			$sender = $name.' <'.$sender.'>';
		$this->sender = $sender;
	}
	
	public function setMessage($message) {
		$this->message = $message;
	}
}

?>