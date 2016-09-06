<?php

class JobG8_UploadAbstractFile
{
	private $jobBoardID;
	private $password;
	private $fileContent = "";
	
	public function setJobBoardID($id)
	{
		$this->jobBoardID = $id;
	}
	
	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function setFileContent($content)
	{
		$this->fileContent = $content;
	}
}