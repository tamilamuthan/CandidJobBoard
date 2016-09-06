<?php

class SJB_Classifieds_SearchForm extends SJB_Function
{
	/**
	 * @return bool
	 */
	public function isAccessible()
	{
		if (SJB_Array::get($this->params, 'listing_type_id') == 'Resume' && !SJB_Settings::getValue('public_resume_access')) {
			$this->setPermissionLabel('resume_access');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		if (isset($_REQUEST['listing_type_id'])) {
			$listing_type_id = $_REQUEST['listing_type_id'];
			SJB_Session::setValue('listing_type_id', $listing_type_id);
		}
		elseif (isset($_REQUEST['restore'])) {
			$listing_type_id = SJB_Session::getValue('listing_type_id');
		}
		else {
			SJB_Session::setValue('listing_type_id', null);
		}

		$listing_type_sid = 0;
		if (!empty($listing_type_id))
			$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);

		if (!isset($_REQUEST['listing_type']['equal']) && isset($listing_type_id))
			$_REQUEST['listing_type']['equal'] = $listing_type_id;

		if (isset($_REQUEST['searchId'])) {
			$criteria_saver = new SJB_ListingCriteriaSaver($_REQUEST['searchId']);
			$_REQUEST = array_merge($_REQUEST, $criteria_saver->getCriteria());
			$tp->assign('searchId', $_REQUEST['searchId']);
		}

		$empty_listing = new SJB_Listing(array(), $listing_type_sid);
		$empty_listing->addIDProperty();
		$empty_listing->addActivationDateProperty();
		$empty_listing->addUsernameProperty();
		$empty_listing->addKeywordsProperty();
		$empty_listing->addListingTypeIDProperty();
		$empty_listing->addPostedWithinProperty();

		$search_form_builder = new SJB_SearchFormBuilder($empty_listing);

		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($_REQUEST);
		
		$properties = $empty_listing->getProperties();
		foreach ($properties as $propertyName => $property) {
			if ($property->getType() == 'location') {
				if (!isset($criteria['system'][$propertyName])) {
					$value = array('value' => '', 'radius' => '50');
					$criterion = SJB_SearchCriterion::getCriterionByType('location');
					$criterion->setProperty($property);
					$criterion->setPropertyName($propertyName);
					$criterion->setValue($value);
					$criteria['system'][$propertyName][] = $criterion;
				}
			}
		}

		$search_form_builder->setCriteria($criteria);
		$search_form_builder->registerTags($tp);

		$form_fields = $search_form_builder->getFormFieldsInfo();
		$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
		$template = SJB_Request::getVar('form_template', 'search_form.tpl');

		if ($template == 'quick_search.tpl') {
			$fieldSID = SJB_ListingFieldManager::getListingFieldSIDByID('Location');
			if ($fieldSID) {
				$fields = SJB_ListingFieldManager::getFieldInfoBySID($fieldSID);
				if (!empty($fields['fields'])) {
					foreach ($fields['fields'] as $field) {
						$form_fields[$fields['id'].'_'.$field['id']] = $field;
					}
				}
				
				$tp->assign('locationFields', array($fields));
			}
		}

		$tp->assign('form_fields', $form_fields);
		$tp->assign(
				'METADATA',
				array(
						'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields),
				)
		);
		if (!empty($this->params['browse_request_data'])) {
			$tp->assign('browse_request_data', $this->params['browse_request_data']);
		}
		$tp->display($template);
	}
}
return;



