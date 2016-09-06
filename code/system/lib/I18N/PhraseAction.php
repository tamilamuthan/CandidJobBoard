<?php


class SJB_PhraseAction
{
	function SJB_PhraseAction()
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

