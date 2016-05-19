<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/4
 * Time: 下午6:00
 */
namespace MiddlewareSpace;

use CommonSpace\Common;

class RetainForWeek
{
    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function baseRetainWeekly($extendStamp = 0, $isRetain = 0, $minCycle = 0, $minBirthCnt = 0, $maxBirthCnt = 0)
    {
        $timestamp = empty($extendStamp) ? strtotime(date('Y-m-d')) : $extendStamp;
        $loginStartStamp = $timestamp - ($isRetain + $minCycle) * 86400;
        $loginEndStamp = $timestamp - $isRetain * 86400;
        $query = array(
            'wct_lt' => array('$gte' => $loginStartStamp, '$lte' => $loginEndStamp),
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
            echo "UnCatch week retain \n";
            return 0;
        }
        $visitEnd = $timestamp - 86400;
        $visitStart = $timestamp - $minCycle * 86400;
        return $this->getRetainCount($userIdCollection, $visitStart, $visitEnd);
    }

    public function getRetainCount($userIdItems = array(), $visitStart = 0, $visitEnd = 0)
    {
        $userIdString = implode(',', $userIdItems);
        $visitStart = "TO_DAYS('" . date('Y-m-d', $visitStart) . "')";
        $visitEnd = "TO_DAYS('" . date('Y-m-d', $visitEnd) . "')";
        $query = sprintf("SELECT COUNT(*) AS user_cnt FROM oibirthday.users AS u WHERE u.id IN ( %s ) AND TO_DAYS(u.visit_on) >= %s AND TO_DAYS(u.visit_on) <= %s", $userIdString, $visitStart, $visitEnd);
        echo $query . "\n";
        $result = $this->connectObj->fetchCnt($query);
        return $result['user_cnt'];

    }

    /**
     * @param $isRetain
     * @return array
     */
    public function getIsRetainWeekParamsKey($isRetain)
    {
        $paramKey = array(
            7 => array(
                1 => 'first_week_core_task_cnt',
                2 => 'first_week_uncore_task_cnt',
            ),
            14 => array(
                1 => 'second_week_core_task_cnt',
                2 => 'second_week_uncore_task_cnt',
            ),
            21 => array(
                1 => 'third_week_core_task_cnt',
                2 => 'third_week_uncore_task_cnt',
            ),
            28 => array(
                1 => 'fourth_week_core_task_cnt',
                2 => 'fourth_week_uncore_task_cnt',
            ),
            35 => array(
                1 => 'fifth_week_core_task_cnt',
                2 => 'fifth_week_uncore_task_cnt',
            ),
            42 => array(
                1 => 'sixth_week_core_task_cnt',
                2 => 'sixth_week_uncore_task_cnt',
            ),
            49 => array(
                1 => 'seventh_week_core_task_cnt',
                2 => 'seventh_week_uncore_task_cnt',
            ),
            56 => array(
                1 => 'eighth_week_core_task_cnt',
                2 => 'eighth_week_uncore_task_cnt',
            ),
            63 => array(
                1 => 'ninth_week_core_task_cnt',
                2 => 'ninth_week_uncore_task_cnt',
            )
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