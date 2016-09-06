<?php

class SJB_BlogPost extends SJB_Object
{
	public function __construct($postInfo = array())
	{
		$this->db_table_name = 'blog';
		$this->details = new SJB_BlogPostDetails($postInfo);
	}
}
