<?php

class SJB_Admin_Miscellaneous_TaskSchedulerSettings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_task_scheduler');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action');
		$template = 'task_scheduler_settings.tpl';

		$tp->assign('cronPath', SJB_BASE_DIR . 'cron/index.php');

		if ($action != 'log_view') {

			$last_executed_date = SJB_System::getSettingByName('task_scheduler_last_executed_date');

			$res = SJB_DB::query("SELECT * FROM `task_scheduler_log` ORDER BY `sid` DESC LIMIT 1");

			$tp->assign('last_executed_date', $last_executed_date);
			$tp->assign('task_scheduler_log', array_pop($res));
		}
		else {
			$log_file = array();
			$res = SJB_DB::query("SELECT `log_text` FROM `task_scheduler_log` ORDER BY `sid` DESC LIMIT 30");
			foreach ($res as $record) {
				$text = $record['log_text'];
				if ($text)
					$log_file[] = $text;
			}

			$tp->assign('log_content', $log_file);
			$template = 'task_scheduler_log_view.tpl';
		}

		$tp->display($template);
	}
}