<?php

namespace SimpleForm\Rules;

class Email extends Format
{

	public function __construct ($errorMsg)
	{
		parent::__construct($errorMsg, '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|pro)$/i');
	}
}
