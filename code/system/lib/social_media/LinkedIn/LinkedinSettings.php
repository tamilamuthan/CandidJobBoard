<?php

return array(
	'JobCategory' => $oLF->get_Industry('array', 'array', 'JobCategory'),
	'Title' => $oLF->get_Positions_Position_Title(),
	'WorkExperience' => $oLF->get_PositionsArr(
		array(
			'start-date' => 'WE_From',
			'end-date' => 'WE_To',
			'title' => 'WE_JobTitle',
			'company-name' => 'WE_Company',
			'summary' => 'WE_Description',
		)
	),
	'Skills' => $oLF->get_Summary(),
);
