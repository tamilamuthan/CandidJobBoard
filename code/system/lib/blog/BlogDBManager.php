<?php

class SJB_BlogDBManager extends SJB_ObjectDBManager
{

    public static function saveBlogPost($articleObject)
    {
        parent::saveObject('blog', $articleObject);
    }

    public static function delete($articleSid)
    {
        parent::deleteObjectInfoFromDB('blog', $articleSid);
    }

    /**
     * @param integer $postId
     * @return string|false
     */
    public static function getImageFileIDByArticleSID($postId)
    {
        $result = SJB_DB::query("SELECT `image` FROM `blog` WHERE `sid` = ?n", $postId);
        if (empty($result)) {
            return false;
        }
        return $result[0]['image'];
    }

    /**
     * @param integer $itemSID
     * @return array|null
     */
    public static function activateItemBySID($itemSID)
    {
        return SJB_DB::query("UPDATE `blog` SET `active` = 1 WHERE `sid` = ?n", $itemSID);
    }

    /**
     * @param integer $itemSID
     * @return array|null
     */
    public static function deactivate($itemSID)
    {
        return SJB_DB::query("UPDATE `blog` SET `active` = 0 WHERE `sid` = ?n", $itemSID);
    }
}