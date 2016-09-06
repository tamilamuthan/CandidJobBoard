<?php

class SJB_LanguageAction
{
	function SJB_LanguageAction()
	{
		$this->errors = array();
	}
	
	function perform()
	{
	}

	function canPerform()
	{
		$this->errors[] = 'UNKNOWN_ACTION';
		return false;
	}

	function getErrors()
	{
		return $this->errors;
	}
}


