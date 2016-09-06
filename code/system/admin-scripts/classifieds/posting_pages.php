<?php
class SJB_Admin_Classifieds_PostingPages extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_posting_pages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$passed_parameters_via_uri = SJB_Request::getVar('passed_parameters_via_uri', false);
		$listing_type_id = '';
		$action = SJB_Request::getVar('action', 'list');
		$pageSID = SJB_Request::getVar('page_sid', 0);

		if ($passed_parameters_via_uri) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$listing_type_id = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
			$action = isset($passed_parameters_via_uri[1]) ? $passed_parameters_via_uri[1] : $action;
			$pageSID = isset($passed_parameters_via_uri[2]) ? $passed_parameters_via_uri[2] : $pageSID;
		}
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		$errors = array();
		$template = 'input_page_form.tpl';
		if ($listing_type_sid) {
			$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_type_sid);
			switch ($action) {
				case 'edit':
					$field_action = SJB_Request::getVar('field_action');
					$pageInfo = SJB_PostingPagesManager::getPageInfoBySID($pageSID);
					$pageInfo = array_merge($pageInfo, $_REQUEST);
					$page = new SJB_PostingPages($pageInfo, $listing_type_sid);
					$page->setSID($pageSID);
					$form = new SJB_Form($page);
					$form->registerTags($tp);
					$form_fields = $form->getFormFieldsInfo();
					switch ($field_action) {
						case 'move_down':
							$field_sid = SJB_Request::getVar('field_sid', null);
							SJB_PostingPagesManager::moveDownFieldBySID($field_sid, $pageSID);
							break;
						case 'move_up':
							$field_sid = SJB_Request::getVar('field_sid', null);
							SJB_PostingPagesManager::moveUpFieldBySID($field_sid, $pageSID);
							break;
						case 'save_order':
							$item_order = SJB_Request::getVar('item_order', null);
							SJB_PostingPagesManager::saveNewJobFieldsOrder($item_order, $pageSID);
							break;
					}

					$listing_fields = SJB_PostingPagesManager::getListingFieldsInfo($listing_type_sid);
					$fieldsOnPage = SJB_PostingPagesManager::getAllFieldsByPageSID($pageSID);
					$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing_type_sid);
					$tp->assign('pageInfo', $pageInfo);
					$tp->assign('pages', $pages);
					$tp->assign('countPages', count($pages));
					$tp->assign("pageSID", $pageSID);
					$tp->assign("fieldsOnPage", $fieldsOnPage);
					$tp->assign("form_fields", $form_fields);
					$tp->assign("listing_fields", $listing_fields);
					break;
			}

			$tp->assign('listingTypeInfo', $listingTypeInfo);
		} else {
			$errors['UNDEFINED_LISTING_TYPE_ID'] = 1;
		}
		$tp->assign('action', $action);
		$tp->assign('errors', $errors);
		$tp->display($template);
	}
}