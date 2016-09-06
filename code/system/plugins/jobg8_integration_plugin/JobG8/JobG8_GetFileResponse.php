<?php

class JobG8_GetFileResponse
{
	private $jobBoardID;
	private $password;
	public $fileName = '';
	
	public function setJobBoardID($id)
	{
		$this->jobBoardID = $id;
	}
	
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	function setFileName($fileName)
	{
		$this->fileName = $fileName;
	}
}