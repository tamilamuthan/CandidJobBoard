<?php

class SJB_Admin_TemplateManager_EditCss extends SJB_Function
{
	public function execute()
	{
		if (SJB_System::getSystemSettings("isSaas")) {
			return;
		}
		$tp = SJB_System::getTemplateProcessor();
		$theme = SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default');
		$themePath = SJB_TemplatePathManager::getAbsoluteThemePath($theme);
		$files = $this->getCssFiles($themePath);

		$tp->assign("action", SJB_Request::getVar("action"));
		switch (SJB_Request::getVar("action")) {
			case "save":
				$fp = fopen($_REQUEST["file"], "w");
				fwrite($fp, $_REQUEST["file_content"]);
				fclose($fp);
			case "edit":
				$tp->assign("file_content", file_get_contents($_REQUEST["file"]));
				$tp->assign("cssFile", $_REQUEST["file"]);
				break;
		}

		$tp->assign("files", $files);
		$tp->display("edit_css.tpl");
	}

	function getCssFiles($dir) {
		$d = dir($dir);
		$files = array();
		while (false !== ($entry = $d->read())) {
			if ($entry == '.' || $entry == '..')
				continue;
			$path = $dir . $entry;
			if (is_dir($path))
				$files = array_merge($files, $this->getCssFiles($path . "/"));
			if (is_file($path)) {
				$pathinfo = pathinfo($path);
				if (isset($pathinfo["extension"]) && strtolower($pathinfo["extension"]) == "css") {
					$files[] = $path;
				}
			}
		}
		return $files;
	}
}
