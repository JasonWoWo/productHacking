<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/5
 * Time: 下午5:23
 */
namespace MiddlewareSpace;

use CommonSpace\Common;

class RetainForMonth
{
    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function baseRetainMonthly(\DateTime $pointDate, \DateTime $visitDate, $minBirthCnt = 0, $maxBirthCnt = 0)
    {
        // 用户注册月的天数
        $pointMonthDays = intval(date('t', $pointDate->getTimestamp()));
        $visitMonthDays = date('t', $visitDate->getTimestamp());
        $loginStartStamp = $pointDate->getTimestamp();
        $loginEndStamp = $pointDate->getTimestamp() + ($pointMonthDays - 1) * 86400;
        $query = array(
            'mct_lt' => array('$gte' => $loginStartStamp, '$lte' => $loginEndStamp),
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
            echo "UnCatch Month retain \n";
            return 0;
        }
        $visitEnd = $visitDate->getTimestamp() + ($visitMonthDays -1) * 86400;
        $visitStart = $visitDate->getTimestamp();
        return $this->getRetainMonthCount($userIdCollection, $visitStart, $visitEnd);
    }

    public function getRetainMonthCount($userIdItems = array(), $visitStart = 0, $visitEnd = 0)
    {
        $userIdString = implode(',', $userIdItems);
        $visitStart = "TO_DAYS('" . date('Y-m-d', $visitStart) . "')";
        $visitEnd = "TO_DAYS('" . date('Y-m-d', $visitEnd) . "')";
        $query = sprintf("SELECT COUNT(*) AS user_cnt FROM oibirthday.users AS u WHERE u.id IN ( %s ) AND TO_DAYS(u.visit_on) >= %s AND TO_DAYS(u.visit_on) <= %s", $userIdString, $visitStart, $visitEnd);
        $result = $this->connectObj->fetchCnt($query);
        return $result['user_cnt'];

    }

    public function getIsRetainMonthParamsKey($isRetain)
    {
        $paramKey = array(
            2 => array(
                1 => 'first_month_core_task_cnt',
                2 => 'first_month_uncore_task_cnt',
            ),
            3 => array(
                1 => 'second_month_core_task_cnt',
                2 => 'first_month_uncore_task_cnt',
            ),
            4 => array(
                1 => 'third_month_core_task_cnt',
                2 => 'third_month_uncore_task_cnt',
            ),
            5 => array(
                1 => 'fourth_month_core_task_cnt',
                2 => 'fourth_month_uncore_task_cnt',
            ),
            6 => array(
                1 => 'fifth_month_core_task_cnt',
                2 => 'fifth_month_uncore_task_cnt',
            ),
            7 => array(
                1 => 'sixth_month_core_task_cnt',
                2 => 'sixth_month_uncore_task_cnt',
            ),
            8 => array(
                1 => 'seventh_month_core_task_cnt',
                2 => 'seventh_month_uncore_task_cnt',
            ),
            9 => array(
                1 => 'eighth_month_core_task_cnt',
                2 => 'eighth_month_uncore_task_cnt',
            ),
            10 => array(
                1 => 'ninth_month_core_task_cnt',
                2 => 'ninth_month_uncore_task_cnt',
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