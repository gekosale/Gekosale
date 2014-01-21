<?php

namespace Gekosale;

class CsrfProtectionModel
{
	protected $_codes = array();
	protected $_init = false;

	public function __construct ()
	{
		if (! $this->_init){
			$this->_init = true;
			
			if (($this->_codes = App::getContainer()->get('session')->getActiveCsrf()) == ''){
				$this->_codes[] = sha1(session_id() . microtime(1));
				App::getContainer()->get('session')->setActiveCsrf($this->_codes);
			}
		}
	}

	public function getCode ()
	{
		return end($this->_codes);
	}

	public function isValid ($code)
	{
		return in_array($code, $this->_codes, true);
	}

	public function reindex ()
	{
		// generowanie nowego kodu
		$this->_codes[] = sha1(session_id() . microtime(1));
		
		// pozostawienie tylko 75 ostatnich kodow
		App::getContainer()->get('session')->setActiveCsrf(array_slice($this->_codes, -75));
	}
}