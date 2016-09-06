<?php

class SJB_StatisticsExportController
{
	public static function createExportDirectory()
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		if (!is_dir($export_files_dir)) {
			mkdir($export_files_dir, 0777);
		}
		return true;
	}
}
