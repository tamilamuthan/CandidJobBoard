<?php

class SJB_Admin_I18n_EditPhrase extends SJB_Function
{
	public function execute()
	{
		$errors = array();
		$result = true;

		$phrases = SJB_Request::getVar('phrases');

		$i18n = SJB_ObjectMother::createI18N();
		$template_processor = SJB_System::getTemplateProcessor();
		$langData = $i18n->getLanguageData($i18n->getCurrentLanguage());

		$domain_id = 'Frontend';
		$i = 0;
		foreach ($phrases as $phrase) {
			$i++;
//			if ($i18n->phraseExists($phrase['name'], $domain_id)) {
				$action = SJB_PhraseActionFactory::get('update_phrase', array(
					'phrase' => $phrase['name'],
					'domain' => 'Frontend',
					'translations' => array(
						$langData['id'] => $phrase['value']
					)
				), $template_processor);

//				if ($action->canPerform()) {
					$action->perform($i == count($phrases));
					$result &= $action->result == 'saved';
//				} else {
//					$errors = $action->getErrors();
//				}
//			} else {
//				$errors[] = 'PHRASE_NOT_EXISTS';
//			}
		}
		if ($errors || empty($result)) {
			echo 'error';
		} else {
			echo 'ok';
		}
		exit();
	}
}
