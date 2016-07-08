<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午3:06
 */
namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilTool;

class UserRegisterRetainQuery
{
    use UtilTool;
    
    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getCurrentRankMonthRegisterRetainCnt(\DateTime $pointDate, \DateTime $visitDate)
    {
        $pointMonthDays = intval(date('t', $pointDate->getTimestamp()));
        $visitMonthDays = intval(date('t', $visitDate->getTimestamp()));
        $loginEndStamp = $pointDate->getTimestamp() + ($pointMonthDays - 1) * 86400;
        $loginStartString = "TO_DAYS('" . $pointDate->format('Y-m-d') . "')";
        $loginEndString = "TO_DAYS('" . date('Y-m-d', $loginEndStamp) . "')";
        $visitEnd = $visitDate->getTimestamp() + ($visitMonthDays -1) * 86400;
        $visitStartString = "TO_DAYS('" . $visitDate->format('Y-m-d') . "')";
        $visitEndString = "TO_DAYS('" . date('Y-m-d', $visitEnd) . "')";
        $sql = sprintf("
        SELECT 
	COUNT(*) AS cnt 
FROM 
	oibirthday.users AS u 
WHERE 
	TO_DAYS(u.create_on) >= %s 
	AND TO_DAYS(u.create_on) <= %s 
	AND TO_DAYS(u.visit_on) >= %s 
	AND TO_DAYS(u.visit_on) <= %s
	AND u.appid IN (1001, 1002, 1003)
	",
            $loginStartString,
            $loginEndString,
            $visitStartString,
            $visitEndString);
        echo $sql . "\n";
        $query = $this->connectObj->fetchCnt($sql);
        return $query['cnt'];
    }

    public function getCurrentRankRegisterRetainCnt($currentStamp = 0, $isRetain = 0, $minCycle = 0)
    {
        $currentDate = date('Y-m-d', $currentStamp);
        $visitDate = new \DateTime($currentDate);
        $visitDate->modify('-1 days');
        $loginEndDate = $this->connectObj->calculateLoginIn($currentStamp, $isRetain);
        $startRank = $isRetain + $minCycle;
        $loginStartDate = $this->connectObj->calculateLoginIn($currentStamp, $startRank);
        $loginStartString = "TO_DAYS(" . $loginStartDate . ")";

        $visitStartDate = "'" .$visitDate->format('Y-m-d') . "'";
        if ($minCycle) {
            $loginEndDate = $this->connectObj->calculateLoginIn($visitDate->getTimestamp(), $isRetain);
            $visitStartDate = $this->connectObj->calculateLoginIn($currentStamp, $minCycle);
        }
        $loginEndString = "TO_DAYS(" . $loginEndDate . ")";
        $visitStartString = "TO_DAYS(" . $visitStartDate . ")";
        $visitEndString = "TO_DAYS('" . $visitDate->format('Y-m-d') . "')";
        $sql = sprintf("
        SELECT 
	COUNT(*) AS cnt 
FROM 
	oibirthday.users AS u 
WHERE 
	TO_DAYS(u.create_on) >= %s 
	AND TO_DAYS(u.create_on) <= %s 
	AND TO_DAYS(u.visit_on) >= %s 
	AND TO_DAYS(u.visit_on) <= %s
	AND u.appid IN (1001, 1002, 1003)
	",
            $loginStartString,
            $loginEndString,
            $visitStartString,
            $visitEndString);
        echo $sql . "\n";
        $query = $this->connectObj->fetchCnt($sql);
        return $query['cnt'];
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
        echo $sql . "\n";
        $query = $this->connectObj->fetchCnt($sql);
        if ($query['cnt']) {
            return true;
        }
        return false;
    }

    public function getIsRetainMonthParamsKey($isRetain)
    {
        $paramsKeys = array(
            2 => 'first_month_user_cnt',
            3 => 'second_month_user_cnt',
            4 => 'third_month_user_cnt',
            5 => 'fourth_month_user_cnt',
            6 => 'fifth_month_user_cnt',
            7 => 'sixth_month_user_cnt',
            8 => 'seventh_month_user_cnt',
            9 => 'eighth_month_user_cnt',
            10 => 'ninth_month_user_cnt',
        );
        return $paramsKeys[$isRetain];
    }
}