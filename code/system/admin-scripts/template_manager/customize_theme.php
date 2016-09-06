<?php

class SJB_Admin_TemplateManager_CustomizeTheme extends SJB_Function
{
	private function uploadFile($name, $dir, $varName = false)
	{
		$theme = SJB_Settings::getValue('TEMPLATE_USER_THEME');
		$fm = new SJB_UploadPictureManager();
		if (isset($_FILES[$name]['error'])) {
			if (SJB_UploadFileManager::getErrorId($name) === false && $fm->isValidUploadedPictureFile($name)) {
				// fixme: to remove or not to remove old file as it can be used somewhere
				move_uploaded_file($_FILES[$name]['tmp_name'], $dir . '/' . $_FILES[$name]['name']);
				SJB_Settings::saveSetting($varName ?: ('theme_' . $name . '_' . $theme), $_FILES[$name]['name']);
				return true;
			} else {
				switch (SJB_UploadFileManager::getErrorId($name)) {
					case 'UPLOAD_ERR_INI_SIZE':
						return 'File size shouldn\'t be larger than 5 MB.';
						break;
					case 'UPLOAD_ERR_NO_FILE':
						return true;
					default:
						return 'File upload error';
				}
			}
		}
		return true;
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$theme = SJB_Settings::getValue('TEMPLATE_USER_THEME');
		$errors = [];
		$themeSettings = ThemeManager::getThemeSettings($theme);

		if (SJB_Request::getMethod() == SJB_Request::METHOD_POST && SJB_Request::getVar('action') == 'save') {
			$files = [
				['name' => 'logo', 				'message' => 'Failed to upload logo: ', 'dir' => SJB_TemplatePathManager::getAbsoluteThemePath($theme) . 'assets/images'],
				['name' => 'favicon', 			'message' => 'Failed to upload favicon: ', 'dir' => SJB_TemplatePathManager::getAbsoluteThemePath($theme) . 'assets/images'],
				['name' => 'main_banner', 		'message' => 'Failed to upload main banner: ', 'dir' => SJB_TemplatePathManager::getAbsoluteThemePath($theme) . 'assets/images'],
				['name' => 'secondary_banner', 	'message' => 'Failed to upload secondary banner: ', 'dir' => SJB_TemplatePathManager::getAbsoluteThemePath($theme) . 'assets/images'],
				['name' => 'banner_top_img', 		'message' => 'Failed to upload banner: ', 'dir' => SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY') . '/banners', 'var' => 'banner_top_img'],
				['name' => 'banner_bottom_img', 	'message' => 'Failed to upload banner: ', 'dir' => SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY') . '/banners', 'var' => 'banner_bottom_img'],
				['name' => 'banner_right_side_img', 'message' => 'Failed to upload banner: ', 'dir' => SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY') . '/banners', 'var' => 'banner_right_side_img'],
				['name' => 'banner_inline_img', 	'message' => 'Failed to upload banner: ', 'dir' => SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY') . '/banners', 'var' => 'banner_inline_img'],
			];
			foreach ($files as $file) {
				$uploadResult = $this->uploadFile($file['name'], $file['dir'], !empty($file['var']) ? $file['var'] : false);
				if ($uploadResult !== true) {
					$errors[] = $file['message'] . $uploadResult;
				}
			}
			if (!$errors) {
				foreach (array_keys($themeSettings) as $availableSetting) {
					if (in_array($availableSetting, ['logo', 'favicon', 'main_banner', 'secondary_banner'])) {
						continue;
					}
					if (strpos($availableSetting, 'banner_') === 0) {
						continue;
					}
					SJB_Settings::saveSetting('theme_' . $availableSetting . '_' . $theme, SJB_Request::getVar($availableSetting));
				}
				$banners = [
					'banner_top',
					'banner_bottom',
					'banner_right_side',
					'banner_inline',
				];
				foreach ($banners as $banner) {
					$item = $banner . '_type';
					$type = SJB_Request::getVar($item);
					SJB_Settings::saveSetting($item, $type);
					if ($type == 'code' && $themeSettings[$banner . '_img']) {
						@unlink(SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY') . '/banners/' . $themeSettings[$banner . '_img']);
						SJB_Settings::saveSetting($banner . '_img', '');
					}
					$item = $banner . '_code';
					SJB_Settings::saveSetting($item, SJB_Request::getVar($item));
					$item = $banner . '_link';
					SJB_Settings::saveSetting($item, SJB_Request::getVar($item));
				}

				ThemeManager::reset();
				$themeSettings = ThemeManager::getThemeSettings($theme);
				ThemeManager::compileStyles();
			}
		}
		$tp->assign('settings', SJB_Settings::getSettings());
		$tp->assign('theme_settings', $themeSettings);
		$tp->assign('fonts', SJB_FontsManager::getFonts());
		$tp->assign('errors', $errors);
		$tp->assign('tab', SJB_Request::getVar('tab'));
		$tp->display('customize_theme.tpl');
	}
}
