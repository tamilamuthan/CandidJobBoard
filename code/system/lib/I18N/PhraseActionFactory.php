<?php


class SJB_PhraseActionFactory
{
	public static function get($action, $params, $template_processor)
	{
		$i18n = SJB_ObjectMother::createI18N();
		$storage = SJB_InfoStorage::createTranslationFilterStorage();
		
		switch ($action)
		{
			case "search_phrases":
				
				$searchPhraseAction = new SJB_SearchPhraseAction($i18n, $params, $template_processor);
				$storePhraseSearchCriteriaAction = new SJB_StorePhraseSearchCriteriaAction($storage, $params);
				
				$phraseAction = new SJB_SerialActionBatch();
				$phraseAction->addAction($searchPhraseAction);
				$phraseAction->addAction($storePhraseSearchCriteriaAction);
				$phraseAction->result = '';
				break;
				
			case "remember_previous_state":
				
				// Criteria are passed by reference to be accessible in the
				// RestorePhraseSearchCriteriaAction and the SearchPhraseAction.
				// So in the RestorePhraseSearchCriteriaAction it can be got from the storage
				// and in the SearchPhraseAction it can be used to search phrases.
				$criteria = null;
				$searchPhraseAction = new SJB_SearchPhraseAction($i18n, $criteria, $template_processor);
				$restorePhraseSearchCriteriaAction = new SJB_RestorePhraseSearchCriteriaAction($storage, $criteria);
				
				$phraseAction = new SJB_SerialActionBatch();
				$phraseAction->addAction($restorePhraseSearchCriteriaAction);
				$phraseAction->addAction($searchPhraseAction);
				$phraseAction->result = '';
				break;
				
			case "update_phrase":
				$phraseAction = new SJB_UpdatePhraseAction($i18n, $params);
				$phraseAction->result = 'saved';
				break;
				
			default:
				$phraseAction = new SJB_PhraseAction();
				break;
		}
		
		return $phraseAction;
	}
}
