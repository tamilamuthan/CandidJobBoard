<?php

class SJB_PictureType extends SJB_UploadFileType
{
	public function __construct($property_info)
	{
		parent::__construct($property_info);
		$this->default_template = 'picture.tpl';
		if (!empty($_REQUEST[$this->property_info['id'] . '_url'])) {
			$this->property_info['value'] = $_REQUEST[$this->property_info['id'] . '_url'];
		}
	}
	
	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$upload_manager = new SJB_UploadPictureManager();
		$upload_manager->setFileGroup("pictures");
		$propertyVariables['width'] = $this->property_info['width'];
		$propertyVariables['height'] = $this->property_info['height'];
		if (strpos($this->property_info['value'], 'http') === 0) {
			return array_merge($propertyVariables, array(
				'value' => array(
					'file_url' => $this->property_info['value'],
					'file_name' => $this->property_info['value'],
					'file_id' => $this->property_info['value'],
					'url' => $this->property_info['value'],
				)
			));
		}
		$newPropertyVariables = array(
			'value'	=> array(
				'file_url' => $upload_manager->getUploadedFileLink($this->property_info['value']),
				'file_name' => $upload_manager->getUploadedFileName($this->property_info['value']),
				'file_id' => $this->property_info['value'],
			),
		);

		return array_merge($propertyVariables, $newPropertyVariables);
	}
	
	function getValue()
	{
		$upload_manager = new SJB_UploadPictureManager();
		$upload_manager->setFileGroup("pictures");
		if (strpos($this->property_info['value'], 'http') === 0) {
			return array(
				'value' => array(
					'file_url' => $this->property_info['value'],
					'file_name' => $this->property_info['value'],
					'file_id' => $this->property_info['value'],
				)
			);
		}
		return array(
			'file_url' => $upload_manager->getUploadedFileLink($this->property_info['value']),
			'file_name' => $upload_manager->getUploadedFileName($this->property_info['value']),
			'file_id' => $this->property_info['value'],
		);
	}

	function isValid()
	{
		if (strpos($this->property_info['value'], 'http') === 0) {
			return @getimagesize($this->property_info['value']) !== false;
		}
		if (empty($_FILES[$this->property_info['id']]['name'])) {
			return true;
		}
		$file_id = $this->property_info['id'] . "_" .$this->object_sid;
		$this->property_info['value'] = $file_id;
		$upload_manager = new SJB_UploadPictureManager();
		if ($upload_manager->isValidUploadedPictureFile($this->property_info['id'])) {
			return true;
		}
		return $upload_manager->getError();
	}
	
	function getSQLValue()
	{
		if (is_array($this->property_info['value']) && ! empty($this->property_info['value']['import'])) {
			return $this->property_info['value']['import'];
		} else {
			if (strpos($this->property_info['value'], 'http') === 0) {
				$imageInfo = @getimagesize($this->property_info['value']);
				if (!$imageInfo) {
					return '';
				}
				$_FILES[$this->property_info['id']] = array(
					'type' => 'application/octet-stream',
					'tmp_name' => $this->property_info['value'],
					'name' => md5(microtime(true)) . image_type_to_extension($imageInfo[2], true),
				);
			}
			$fileId = $this->property_info['id'] . '_' .$this->object_sid;
			$this->property_info['value'] = $fileId;
			$uploadManager = new SJB_UploadPictureManager();
			$uploadManager->setUploadedFileID($fileId);
			$uploadManager->setHeight($this->property_info['height']);
			$uploadManager->setWidth($this->property_info['width']);
			$uploadManager->uploadPicture($this->property_info['id']);
			if (SJB_UploadPictureManager::doesFileExistByID($fileId)) {
				return $fileId;
			}
			return '';
		}
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'width',
				'caption'	=> 'Width', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> '100',
				'is_system' => true,
				'is_required'=> true,
				),
			array(
				'id'		=> 'height',
				'caption'	=> 'Height', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> '100',
				'is_system' => true,
				'is_required'=> true,
				),
		);
	}
}
