<?php

class SJB_BlogManager extends SJB_ObjectManager
{
	static $uploadFileManager = null;
	
	/**
	 * get instance of upload file manager
	 * @return SJB_UploadFileManager
	 */
	private static function getUploadFileManager()
	{
		if (self::$uploadFileManager == null)
			self::$uploadFileManager = new SJB_UploadFileManager();
		return self::$uploadFileManager;
	}

	/**
	 * Save article in database
	 * @param SJB_BlogPost $articleObject
	 */
	public static function saveBlogPost($articleObject)
	{
		SJB_BlogDBManager::saveBlogPost($articleObject);
	}

	/**
	 * delete article by SID
	 * @param integer $postId
	 */
	public static function delete($postId)
	{
		// delete picture
		$image = SJB_BlogDBManager::getImageFileIDByArticleSID($postId);
		if ($image) {
			$uploadFileManager = self::getUploadFileManager();
			$uploadFileManager->deleteUploadedFileByID($image);
		}
		SJB_BlogDBManager::delete($postId);
	}

	/**
	 * Delete image from blog post
	 * @param integer $postId
	 */
	public static function deleteBlogPostImage($postId)
	{
		// delete picture
		$image = SJB_BlogDBManager::getImageFileIDByArticleSID($postId);
		if ($image) {
			$uploadFileManager = self::getUploadFileManager();
			$uploadFileManager->deleteUploadedFileByID($image);
		}
	}

	/**
	 * get count of all posts
	 * @return integer
	 */
	public static function getAllPostsCount()
	{
		return SJB_DB::queryValue('SELECT count(*) as count FROM `blog`');
	}

	/**
	 * activate blog post by id
	 * @param integer $postId
	 * @return array|null
	 */
	public static function activate($postId)
	{
		return SJB_BlogDBManager::activateItemBySID($postId);
	}

	/**
	 * deactivate blog post by id
	 * @param integer $postId
	 * @return array|null
	 */
	public static function deactivate($postId)
	{
		return SJB_BlogDBManager::deactivate($postId);
	}

	public static function getBlogPostInfoBySid($sid, $forView = false)
	{
		$post = SJB_BlogDBManager::getObjectInfo('blog', $sid);
		$fm = new SJB_UploadPictureManager();
		if ($forView && $post['image']) {
			$post['image'] = $fm->getUploadedFileLink($post['image']);
		}
		return $post;
	}

	public static function getBlogPostBySid($sid)
	{
		$articleInfo = self::getBlogPostInfoBySid($sid);
		$articleObj  = null;
		if (!empty($articleInfo)) {
			$articleObj  = new SJB_BlogPost($articleInfo);
			$articleObj->setSID($sid);
		}
		return $articleObj;
	}


	// TODO: fix to work with objects
	/**
	 * Get all articles by category ID
	 *
	 * @param string $sortingField
	 * @param string $sortingOrder
	 * @param int $page
	 * @param int $itemsPerPage
	 * @return array|null
	 */
	public static function getBlogPosts($sortingField = 'date', $sortingOrder = 'DESC', $page = 1, $itemsPerPage = 10, $activeOnly = false)
	{
		$start = ($page - 1) * $itemsPerPage;
		if ($sortingOrder != 'ASC' && $sortingOrder != 'DESC') {
			$sortingOrder = 'ASC';
		}
		$active = '';
		if ($activeOnly) {
			$active = 'WHERE `active` = 1';
		}
		$posts = SJB_DB::query("SELECT * FROM `blog` {$active} ORDER BY `{$sortingField}` {$sortingOrder}, `sid` DESC LIMIT ?n, ?n", $start, $itemsPerPage);
		$fm = new SJB_UploadFileManager();
		foreach ($posts as $key => $post) {
			if ($post['image']) {
				$posts[$key]['image'] = $fm->getUploadedFileLink($post['image']);
			}
		}
		return $posts;
	}
}
