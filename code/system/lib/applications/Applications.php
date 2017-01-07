<?php

class SJB_Applications
{
    const APPLICATION_SETTINGS_TYPE_EMAIL = 1;
    const APPLICATION_SETTINGS_TYPE_URL   = 2;

	public static function getById($id)
    {
        $res = SJB_DB::query("select * from applications where id = ?s", $id);
        if (count($res) > 0)
            return array_shift($res);
        return false;
    }

	public static function getByJob($listingID, $orderInfo = false, $limit = false, $score=false)
	{
		$order = SJB_Applications::generateOrderAndJoin($orderInfo);

		$limitFilter = '';
		if (!empty($limit)) {
			$limitFilter = "LIMIT {$limit['startRow']}, {$limit['countRows']}";
		}

        $scoreFilter['case'] = '';
        $scoreFilter['inner_join'] = '';
        if (!empty($score)) {
            $scoreFilter = self::getScoredApplications($score);
        }
        
		$apps = SJB_DB::query("
		SELECT `a`.*
		FROM `applications` `a`
			INNER JOIN `listings` l ON
				`l`.`sid` = `a`.`listing_id`
                {$scoreFilter['inner_join']}
				{$order['join']}
				WHERE `a`.`listing_id` = ?s {$scoreFilter['case']} {$order['order']} {$limitFilter}", $listingID);
		return $apps;
	}

	public static function getCountAppsByJob($listingID, $score=false)
	{
        $scoreFilter['case'] = '';
        $scoreFilter['inner_join'] = '';
        if (!empty($score)) {
            $scoreFilter = self::getScoredApplications($score);
        }

		$appsCount = SJB_DB::queryValue("
		SELECT
			COUNT(`a`.`listing_id`)
		FROM
			`applications` `a`
		INNER JOIN `listings` l ON
			`l`.`sid` = `a`.`listing_id`
            {$scoreFilter['inner_join']}
		WHERE `a`.`listing_id` = ?s {$scoreFilter['case']}", $listingID);

		return $appsCount;
	}

    public static function getByJobseeker($id, $orderInfo = false)
    {
        $order = SJB_Applications::generateOrderAndJoin($orderInfo);
        return SJB_DB::query("select a.* from `applications` a  {$order['join']} where a.`jobseeker_id` = ?s {$order['order']}", $id);
    }

    public static function generateOrderAndJoin($orderInfo = false)
    {
        $result['order'] = '';
        $result['join'] = '';
        if (isset($orderInfo['inner_join'])) {
            $result['join'] = " LEFT JOIN {$orderInfo['inner_join']['table']} ON  `{$orderInfo['inner_join']['table']}`.`{$orderInfo['inner_join']['field1']}`=a.`{$orderInfo['inner_join']['field2']}`";
            if (isset($orderInfo['sorting_field']))
                $result['order'] = " ORDER BY `{$orderInfo['inner_join']['table']}`.`{$orderInfo['sorting_field']}` {$orderInfo['sorting_order']}";
            if (isset($orderInfo['inner_join2'])) {
                $result['join'] .= " LEFT JOIN {$orderInfo['inner_join2']['table1']} ON  `{$orderInfo['inner_join2']['table1']}`.`{$orderInfo['inner_join2']['field1']}`=`{$orderInfo['inner_join2']['table2']}`.`{$orderInfo['inner_join2']['field2']}`";
                if (isset($orderInfo['sorting_field']))
                    $result['order'] = " ORDER BY `{$orderInfo['inner_join2']['table1']}`.`{$orderInfo['sorting_field']}` {$orderInfo['sorting_order']}";
            }
        }
        else {
            if (isset($orderInfo['sorting_field']))
                $result['order'] = 'ORDER BY a.`'.$orderInfo['sorting_field'].'` '.$orderInfo['sorting_order'];
            elseif (isset($orderInfo['sorting_fields']))
                $result['order'] = " ORDER BY a.`{$orderInfo['sorting_fields']['field1']}` a.`{$orderInfo['sorting_fields']['field2']}` {$orderInfo['sorting_order']}";
        }

        return $result;
    }

	public static function getByEmployer($userSID, $orderInfo, $limit = false, $score=false)
	{
		$order = SJB_Applications::generateOrderAndJoin($orderInfo);

		$limitFilter = '';
		if (!empty($limit)) {
			$limitFilter = "LIMIT {$limit['startRow']}, {$limit['countRows']}";
		}
        
        $scoreFilter['case'] = '';
        $scoreFilter['inner_join'] = '';
        if (!empty($score)) {
            $scoreFilter = self::getScoredApplications($score);
        }

		$apps = SJB_DB::query("
			SELECT `a`.*
			FROM
				`applications` `a`
			INNER JOIN `listings` l ON
				`l`.`sid` = `a`.`listing_id`
                {$scoreFilter['inner_join']}
				{$order['join']}
			WHERE `l`.`user_sid` = ?s {$scoreFilter['case']} {$order['order']} {$limitFilter}", $userSID);
		return $apps;
	}

	public static function getCountApplicationsByEmployer($userSID, $score=false)
	{
        $scoreFilter['case'] = '';
        $scoreFilter['inner_join'] = '';
        if (!empty($score)) {
            $scoreFilter = self::getScoredApplications($score);
        }

		$appsCount = SJB_DB::queryValue("
			SELECT COUNT(`a`.`listing_id`)
			FROM
				`applications` `a`
			INNER JOIN `listings` l ON
				`l`.`sid` = `a`.`listing_id`
                {$scoreFilter['inner_join']}
			WHERE `l`.`user_sid` = ?s {$scoreFilter['case']}", $userSID);
		return $appsCount;
	}

	public static function getBySID($sid)
	{
		$apps = SJB_DB::query("
			SELECT
				`a`.*
			FROM
				`applications` a
			INNER JOIN `listings` l ON
					`l`.`sid` = `a`.`listing_id`
			WHERE a.`id` = ?n", $sid);
		$apps = $apps?array_pop($apps):array();
		return $apps;
	}

    public static function getAppGroupsByEmployer($companyId)
    {
        return SJB_DB::query("
            select a.listing_id, a.id, count(*) as count from `applications` a
            inner join `listings` l on
                 `l`.`sid` = `a`.`listing_id`
            where `user_sid` = ?s GROUP BY `a`.`listing_id`", $companyId);
    }

    /**
     * Is user applied to job posting
     *
     * @param int $listing_id
     * @param int $jobseeker_id
     * @return bool
     */
    public static function isApplied($listing_id, $jobseeker_id)
    {
        if (!$jobseeker_id)
            return false;

        return count(SJB_DB::query("select * from applications where listing_id = ?s and jobseeker_id = ?s", $listing_id, $jobseeker_id)) > 0;
    }

    /**
     * Is user applied to job posting
     *
     * @param int $listing_id
     * @param int $email
     * @return bool
     */
    public static function isAppliedGuest($listing_id, $email)
    {
        return count(SJB_DB::query('select id from applications where listing_id = ?s and email = ?s and jobseeker_id = 0 limit 1', $listing_id, $email)) > 0;
    }

	public static function isListingAppliedForCompany($listing_id, $company_id)
    {
        return count(SJB_DB::query("
            SELECT a. * , l.user_sid FROM `applications` a
            INNER JOIN `listings` l ON l.sid = a.`listing_id`
            WHERE user_sid = ?s AND resume_id = ?s", $company_id, $listing_id)) > 0;
    }

	public static function isUserOwnerApps($user_sid, $apps_sid)
    {
        return count (SJB_DB::query("
            SELECT a. * , l.user_sid FROM `applications` a
            INNER JOIN `listings` l ON l.sid = a.`listing_id`
            WHERE l.user_sid = ?n AND id = ?n", $user_sid, $apps_sid)) > 0;
    }
    
    /**
     * Check if user owns applications By AppJobId 
     *
     * @param int $user_sid
     * @param int $apps_sid
     * @return int
     */
	public static function isUserOwnsAppsByAppJobId($user_sid, $app_job_id)
    {
        return count(SJB_DB::query("
            SELECT a. * , l.user_sid FROM `applications` a
            INNER JOIN `listings` l ON l.sid = a.`listing_id`
            WHERE l.user_sid = ?n AND a.listing_id = ?n", $user_sid, $app_job_id)) > 0;
    }

    /**
     * Creates new application
     *
     * @param int $listing_id
     * @param int $jobseeker_id
     * @param int|string $resume
     * @param $comments
     * @param $file
     * @param $mimeType
     * @param $file_sid
     * @param bool $post
     * @return array|bool
     */
	public static function create($listing_id, $jobseeker_id, $resume, $comments, $file, $mimeType, $file_sid, $post = false, $questionnaire='', $score=0)
    {
        if (SJB_Applications::isApplied($listing_id, $jobseeker_id) && !is_null($jobseeker_id))
            return false;

        $file_id = '';
        if ($file_sid != '') {
            $file_id = SJB_DB::queryValue("SELECT `id` FROM `uploaded_files` WHERE `sid` = ?s", $file_sid);
        }

        $jobSeekerName  = $post['name'];
        $jobSeekerEmail = $post['email'];
        $res = SJB_DB::query("
            insert into applications(`listing_id`, `jobseeker_id`, `comments`, `date`, `resume`, `file`, `mime_type`, `username`, `email`, `file_id`,`questionnaire`,`score`)
            values(?s, ?s, ?s, NOW(), ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s)", $listing_id, $jobseeker_id ? $jobseeker_id : 0, $comments, $resume, $file, $mimeType, $jobSeekerName, $jobSeekerEmail, $file_id, $questionnaire, $score);
        return !empty($res);
    }

	public static function remove($id)
    {
        $fileID = SJB_DB::queryValue("SELECT `file_id` FROM `applications` WHERE `id` = ?s", $id);
        if (!empty($fileID)) {
            SJB_UploadFileManager::deleteUploadedFileByID($fileID);
        }
        SJB_DB::query("delete from applications where id = ?s", $id);
    }

    /**
     * Gets an Application Email from Application Settings
     *
     * @param int $listing_id
     * @return string
     */
	public static function getApplicationEmailbyListingId($listing_id)
    {
    	$application_email = SJB_DB::queryValue("SELECT `value` FROM `listings_properties` WHERE `object_sid` = ?n AND `id` = ?s AND `add_parameter` = ?n AND `value` <> ''", $listing_id, 'ApplicationSettings', 1);
		if ($application_email)
			return $application_email;
		return '';
    }

    public static function getApplicationMeta()
    {
        $meta = array(
            "application" => array (
                "date" => array (
                    "type" => "date"
                )
            )
        );
        return $meta;
    }

    public static function getApplicationsInfo()
    {
        $res = array();

        // условие запроса сформируем в зависимости от требуемого периода
        $periods = array(
            'Today' => '`a`.`date` >= CURDATE()',
            'Last 7 days' => '`a`.`date` >= date_sub(curdate(), interval 7 day)',
            'Last 30 days' => '`a`.`date` >= date_sub(curdate(), interval 30 day)',
            'Total' => '1=1',
        );

        foreach ($periods as $period => $where) {
            $res[$period] = SJB_DB::queryValue('
                select count(*)
                from `applications` a
                where ' . $where);
        }
        return $res;
    }

    public static function removeByListing($listing)
    {
        $applications = SJB_DB::query('select `id`, `file_id` from `applications` where `listing_id` = ?n', $listing);
        foreach ($applications as $application) {
            if ($application['file_id']) {
                SJB_UploadFileManager::deleteUploadedFileByID($application['file_id']);
            }
        }
        SJB_DB::query('delete from `applications` where `listing_id` = ?n', $listing);
    }
    
       /**
     * @param $score
     */
    public static function getScoredApplications($score)
    {
        $scoreFilter['inner_join'] = "
        inner join `screening_questionnaires` `s` on
            `l`.`screening_questionnaire` = `s`.`sid`
        ";

        if ($score == 'passed') {
            $scoreFilter['case'] = "
                AND `a`.`score` >= (CASE `s`.`passing_score`
                WHEN 'acceptable' THEN 1
                WHEN 'good' THEN 2
                WHEN 'very_good' THEN 3
                WHEN 'excellent' THEN 4
                END)
                ";
        } elseif ($score == 'not_passed') {
                $scoreFilter['case'] = "
                AND `a`.`score` < (CASE `s`.`passing_score`
                WHEN 'acceptable' THEN 1
                WHEN 'good' THEN 2
                WHEN 'very_good' THEN 3
                WHEN 'excellent' THEN 4
                END)
                ";
        }
        return $scoreFilter;
    }
    
    public static function accept($applicationId)
    {
        SJB_DB::query("update applications set `status` = 'Approved' where id = ?s", $applicationId);
    }

    public static function reject($applicationId)
    {
        SJB_DB::query("update applications set `status` = 'Rejected' where id = ?s", $applicationId);
    }
}
