<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/4
 * Time: 下午3:33
 */
include __DIR__.'/../common/Common.php';
class RetainForDay
{
    public $connectObj;

    public function __construct(Common $common)
    {
        $this->connectObj = $common;
    }
    
    public function baseRetainDaily($extendStamp = 0, $isRetain = 0, $minBirthCnt = 0, $maxBirthCnt = 0)
    {
        $timestamp = empty($extendStamp) ? strtotime(date('Y-m-d')) : $extendStamp;
        $loginStartStamp = $timestamp - $isRetain * 85400;
        $loginEndStamp = $timestamp - ($isRetain - 1) * 86400;
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
        return $this->getRetainWeekCount($userIdCollection, $loginEndStamp);
    }

    public function getRetainWeekCount($userIdItems = array(), $loginEndStamp = 0)
    {
        $userIdString = implode(',', $userIdItems);
        $currentString = "TO_DAYS('" . date('Y-m-d', $loginEndStamp) . "')";
        $query = sprintf("SELECT COUNT(*) AS user_cnt FROM oibirthday.users AS u WHERE u.id IN ( %s ) AND TO_DAYS(u.visit_on) = %s ", $userIdString, $currentString);
        $result = $this->connectObj->fetchCnt($query);
        return $result['user_cnt'];

    }
    
    public function getIsRetainParamsDailyKey($isRetain) 
    {
        $paramKey = array(
            2 => array(
                1 => 'second_core_task_cnt',
                2 => 'second_uncore_task_cnt',
            ),
            7 => array(
                1 => 'week_core_task_cnt',
                2 => 'week_uncore_task_cnt',
            ),
            15 => array(
                1 => 'half_month_core_task_cnt',
                2 => 'half_month_uncore_task_cnt',
            ),
            30 => array(
                1 => 'month_core_task_cnt',
                2 => 'month_uncore_task_cnt',
            ),
            60 => array(
                1 => 'second_month_core_task_cnt',
                2 => 'second_month_uncore_task_cnt',
            ),
            90 => array(
                1 => 'quarter_month_core_task_cnt',
                2 => 'quarter_month_uncore_task_cnt',
            ),
        );
        return $paramKey[$isRetain];
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