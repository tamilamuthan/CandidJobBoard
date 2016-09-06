<?php

class SJB_UserInfoSearcher extends SJB_ObjectInfoSearcher
{
	var $limit = false;
	var $sorting_field = false;
	var $sorting_order = false;
	var $inner_join = false;
	var $affectedRows = 0;
	var $limitByPHP = false;

	/**
	 * @var SJB_UserSearchSQLTranslator
	 */
	private $searchSqlTranslator;
	
	function __construct($limit = false, $sorting_field = false, $sorting_order = false, $inner_join = false, $limitByPHP)
	{
		parent::__construct('users');
		$this->limit = $limit;
		$this->sorting_field = $sorting_field;
		$this->sorting_order = $sorting_order;
		$this->inner_join = $inner_join;
		$this->limitByPHP = $limitByPHP;
		$this->searchSqlTranslator = new SJB_UserSearchSQLTranslator($this->table_prefix);
	}

	function getObjectInfo($sorting_fields, $inner_join = false, $relevance = false)
	{
		if (isset($this->inner_join['contracts'])) {
			$this->searchSqlTranslator->setDistinct(true);
		}
		$sql_string = $this->searchSqlTranslator->buildSqlQuery($this->criteria, $this->valid_criterion_number, $sorting_fields, $this->inner_join);
		$where = '';
		$groupBy = '';
		if ($this->inner_join){
			foreach ($this->inner_join as $key => $val) {
				if (isset($val['sort_field'])) {
					if (isset($val['noPresix'])) {
						$this->sorting_field = $val['sort_field'];
					} else {
						$this->sorting_field = "`".$key."`.".$val['sort_field'];
					}
				}
				if (isset($val['where'])) {
					$where .= " {$val['where']} ";
				}
				if (isset($val['groupBy'])) {
					$groupBy .= " GROUP BY {$val['groupBy']} ";
				}
				if (isset($val['join']) && $val['join'] != 'INNER JOIN') {
					$this->searchSqlTranslator->setDistinct(true);
				}
			}
		}
		$sql_string .= $where;
        if ($this->sorting_field !== false && $this->sorting_order !== false){
        	$sorting = '';
			if (!is_array($this->sorting_field)) {
				$this->sorting_field = [$this->sorting_field];
			}
			foreach ($this->sorting_field as $sorting_field) {
				if (preg_match('/[^\w-]/ui', $sorting_field)) { // prevent sorting field sql injection
					continue;
				}
				if (!preg_match('/asc|desc/ui', $this->sorting_order)) {
					continue;
				}
				$sorting .= " {$sorting_field} {$this->sorting_order}, ";
			}
			$sorting = trim($sorting, ', ');
			if ($sorting) {
				$sorting = 'ORDER BY ' . $sorting;
			}
        	$sql_string .= " {$groupBy} {$sorting} ";
        }
		$affectedRows = 0;
		if ($this->limit !== false) {
			SJB_DB::queryExec($sql_string);
			$affectedRows = SJB_DB::getAffectedRows();
			if (isset($this->limit['limit'])) {
				$sql_string .= "limit " . $this->limit['limit'] . ", ".$this->limit['num_rows'];
			} else {
				$sql_string .= "limit " . $this->limit . ", 100";
			}
		}
		$sql_results = SJB_DB::query($sql_string);
		if ($this->limit === false) {
			$affectedRows = SJB_DB::getAffectedRows();
		}
		$result = array();
	    foreach ($sql_results as $sql_result) {
			if ($this->valid_criterion_number == 0 || $sql_result['countRows'] == $this->valid_criterion_number)
				$result[]['object_sid'] = $sql_result['object_sid'];
		}
		$this->affectedRows = $affectedRows - (SJB_DB::getAffectedRows() - count($result));
		// TODO написала это потому что в browseCompany неправильно считается общее количество компаний. Например по факту находится одна компания, но пишется, что найдено 16.
		if ($this->limitByPHP !== false) {
			$newArr = $result;
			$result = array();
			for ($i=$this->limitByPHP['limit']; $i<($this->limitByPHP['limit']+$this->limitByPHP['num_rows']); $i++) {
				if (!isset($newArr[$i]))
					break;
				$result[$i] = $newArr[$i];
			}
		}
		return $result;
	}
}
