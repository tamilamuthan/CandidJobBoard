<?php

class SJB_Admin_Users_Acl extends SJB_Function
{
	public function execute()
	{
		// fixme: deprecated
		return;
		$acl = SJB_Acl::getInstance();
		$type = SJB_Request::getVar('type', '');
		$role = SJB_Request::getVar('role', '');
		$tp = SJB_System::getTemplateProcessor();
		$resources = $acl->getResources();
		$form_submitted = SJB_Request::getVar('action');

		if ($form_submitted) {
			SJB_Acl::clearPermissions($type, $role);
			foreach ($resources as $name => $resource) {
				$params = SJB_Request::getVar($name . '_params');
				$message = '';
			    if (SJB_Request::getVar($name) == 'deny') {
		            $params = SJB_Request::getVar($name . '_params1');
		            if ($params == 'message')
						$message = SJB_Request::getVar($name . '_message');
		        }
				SJB_Acl::allow($name, $type, $role, SJB_Request::getVar($name, ''), $params, SJB_Request::getVar($name . '_message'));
			}

			if ($form_submitted == 'save') {
				switch ($type) {
					case 'group' :
						$parameter = "/edit-user-group/?sid=" . $role;
						break;
					case 'guest' :
						$parameter = "/user-groups/";
						break;
				}

				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . $parameter);
			}
		}

		$acl = SJB_Acl::getInstance(true);
		$resources = $acl->getResources($type);
		$perms = SJB_DB::query('select * from `permissions` where `type` = ?s and `role` = ?s', $type, $role);
		foreach ($resources as $key => $resource) {
			$resources[$key]['value'] = 'inherit';
			$resources[$key]['name'] = $key;
			foreach ($perms as $perm) {
				if ($key == $perm['name']) {
					$resources[$key]['value'] = $perm['value'];
					$resources[$key]['params'] = $perm['params'];
					$resources[$key]['message'] = $perm['message'];
					break;
				}
			}
		}

		$tp->assign('resources', $resources);
		$tp->assign('type', $type);
		$tp->assign('listingTypes', SJB_ListingTypeManager::getAllListingTypesInfo());
		$tp->assign('role', $role);

		switch ($type) {
			case 'group':
				$tp->assign('userGroupInfo', SJB_UserGroupManager::getUserGroupInfoBySID($role));
				break;
			case 'user':
				$userInfo = SJB_UserManager::getUserInfoBySID($role);
				$tp->assign('userGroupInfo', SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']));
				break;
		}

		$tp->display('acl.tpl');
	}
}