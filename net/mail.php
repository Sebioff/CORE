<?php

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