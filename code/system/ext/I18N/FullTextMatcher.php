<?php


class FullTextMatcher
{
	function setQuery($query)
	{
		$this->query_tokens = preg_split('/\\s+/', $query);
	}
	
	function match($subject)
	{
		foreach ($this->query_tokens as $token) {
			if ($token === '') {
				continue;
			}
			if (mb_stripos($subject, $token) === false) {
				return false;
			}
		}
		return true;
	}
}