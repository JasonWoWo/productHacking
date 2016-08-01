<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/4
 * Time: 下午3:33
 */
namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class RetainForDay
{
    use UtilSqlTool;
    use UtilTool;
    
    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }
    
    public function baseRetainDaily($extendStamp = 0, $isRetain = 0, $minBirthCnt = 0, $maxBirthCnt = 0)
    {
        $timestamp = empty($extendStamp) ? strtotime(date('Y-m-d')) : $extendStamp;
        $loginStartStamp = $timestamp - $isRetain * $this->default_daily_timestamp;
        $loginEndStamp = $timestamp - ($isRetain - 1) * $this->default_daily_timestamp;
        $query = array(
            'dct_lt' => array('$gte' => $loginStartStamp, '$lte' => $loginEndStamp),
            'max_bct' => array('$gte' => $minBirthCnt, '$lte' => $maxBirthCnt)
        );
        $retainCollection = $this->connectObj->fetchRetainCollection();
        $retainItems = $retainCollection->find($query);
        $userIdCollection = array();
        if (empty($retainItems)) {
            return 0;
        }
        foreach ($retainItems as $item) {
            $userIdCollection[] = $item['uid'];
        }
        if (empty($userIdCollection)) {
            return 0;
        }
        $visitTimeStamp = $extendStamp - $this->default_daily_timestamp;
        return $this->getRetainDailyCount($userIdCollection, $visitTimeStamp);
    }

    public function getRetainDailyCount($userIdItems = array(), $visitTimeStamp = 0)
    {
        $userIdString = implode(',', $userIdItems);
        $query = $this->getQueryRegisterVisitCnt($userIdString, $visitTimeStamp, true);
        $result = $this->connectObj->fetchAssoc($query);
        $userList = array();
        foreach ($result as $item) {
            $userList[] = $item['id'];
        }
        return count($result);
    }

    public function checkCurrentDateData($tableName, $dayString)
    {
        $sql = sprintf("
        SELECT COUNT(*) AS cnt
        FROM %s AS ups
        WHERE ups.`create_on` = %s",
            $tableName,
            $dayString
        );
        $query = $this->connectObj->fetchCnt($sql);
        if ($query['cnt']) {
            return true;
        }
        return false;
    }
}