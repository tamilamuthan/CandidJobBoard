<?php

class SJB_Miscellaneous_404NotFound extends SJB_Function
{
	public function execute()
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found'); // no such page in configuration
		$tp = SJB_System::getTemplateProcessor();
		$tp->display('404.tpl');
	}
}
