<?php
/**
* AUTO-GENERATED
* DO NOT EDIT
*/
require_once CORE_ROOT . 'DAO.php';
class Model_Data_VideoPeopleProviderBase
{
    protected function getOneFromQuery($strSql, $params)
    {
        $arrResults = array();
        $arrErrors = array();
        if (DAO::getAssoc($strSql, $params, $arrResults, $arrErrors)) {
            if (count($arrResults) > 0) {
                return new Model_Structure_VideoPeople($arrResults[0]);
            }
        }
        return null;
    }

    protected function getArrayFromQuery($strSql, $params)
    {
        $arrResults = array();
        $arrErrors = array();
        if (DAO::getAssoc($strSql, $params, $arrResults, $arrErrors)) {
            $arrRecordList = array();
            foreach ($arrResults as $arrRecord) {
                $arrRecordList[] = new Model_Structure_VideoPeople($arrRecord);
            }
            return $arrRecordList;
        }
        return null;
    }

    public function getOneByPk($video_id, $people_id, $relationship)
    {
        $strSql = 'SELECT * FROM `video_people` WHERE video_id=? AND people_id=? AND relationship=?';
        $params = array($video_id, $people_id, $relationship);
        return Model_Data_VideoPeopleProvider::getOneFromQuery($strSql, $params);
    }

    public function insertOne(&$objRecord, &$arrErrors)
    {
        $strSql = ' INSERT INTO `video_people` (
            video_id,
            people_id,
            relationship
        ) VALUES  (?, ?, ?)';
        $params = array($objRecord->getVideoId(),
            $objRecord->getPeopleId(),
            $objRecord->getRelationship()
        );
        $arrErrors = array();
        $blnResult = DAO::execute($strSql, $params, $arrErrors);
        return $blnResult;
    }

    public function replaceOne(&$objRecord, &$arrErrors)
    {
        $strSql = ' REPLACE INTO `video_people` (
            video_id,
            people_id,
            relationship
        ) VALUES  (?, ?, ?)';
        $params = array($objRecord->getVideoId(),
            $objRecord->getPeopleId(),
            $objRecord->getRelationship()
        );
        $arrErrors = array();
        $blnResult = DAO::execute($strSql, $params, $arrErrors);
        return $blnResult;
    }

    public function updateOne($objRecord, &$arrErrors)
    {
        $strSql = 'UPDATE `video_people` SET 
            video_id=?,
            people_id=?,
            relationship=?
        WHERE video_id=? AND people_id=? AND relationship=?';
        $arrSetParams = array(
            $objRecord->getVideoId(),
            $objRecord->getPeopleId(),
            $objRecord->getRelationship()
        );
        $arrKeyParams = array($objRecord->getOrigVideoId(), $objRecord->getOrigPeopleId(), $objRecord->getOrigRelationship());
        $params = array_merge($arrSetParams, $arrKeyParams);
        $arrErrors = array();
        $blnResult = DAO::execute($strSql, $params, $arrErrors);
        return $blnResult;
    }

    public function deleteOne($objRecord, &$arrErrors)
    {
        $strSql = 'DELETE FROM `video_people` WHERE video_id=? AND people_id=? AND relationship=?';
        $params = array($objRecord->getVideoId(), $objRecord->getPeopleId(), $objRecord->getRelationship());
        $arrErrors = array();
        $blnResult = DAO::execute($strSql, $params, $arrErrors);
        return $blnResult;
    }
}
