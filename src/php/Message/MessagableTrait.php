<?php

namespace Catalyst\Message;

/**
 * Used for User and Artist, as they are messagable
 */
trait MessagableTrait {
	/**
	 * Get the URL path to message one of these objects
	 * like User/{username}, will be appended to Message/New
	 * 
	 * @return string Message URL path
	 */
	abstract public function getMessageUrlPath() : string;

	/**
	 * @return string Message URL path with precomposed data
	 */
	public function getPrecomposedMessageUrl(string $subject="", string $message="") : string {
		if (empty($subject) && empty($message)) {
			return $this->getMessageUrlPath();
		} elseif (empty($message)) {
			return $this->getMessageUrlPath().urlencode($subject)."/";
		}
		return $this->getMessageUrlPath().urlencode($subject)."/".urlencode($message)."/";
	}

	/**
	 * Get the friendly name to use for the user
	 * 
	 * Nickname/name as compared to username
	 * 
	 * @return string Friendly name
	 */
	abstract public function getFriendlyName() : string;

	/**
	 * Get a message button for the object
	 * 
	 * @return string message button html
	 */
	public function getMessageButton() : string {
		$str = '';
		$str .= '<p';
		$str .= ' class="flow-text no-top-margin"';
		$str .= '>';
		$str .= '<a';
		$str .= ' href="'.ROOTDIR.'Message/New/'.htmlspecialchars($this->getMessageUrlPath()).'"';
		$str .= ' class="btn"';
		$str .= '>';
		$str .= 'message '.htmlspecialchars($this->getFriendlyName());
		$str .= '</a>';
		$str .= '</p>';
		return $str;
	}
}
