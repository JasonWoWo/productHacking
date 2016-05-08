<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午2:45
 */
include __DIR__.'/../common/Common.php';
class UserRegisterQuery
{
    public $connectObj;

    public function __construct(Common $common)
    {
        $this->connectObj = $common;
    }

    public function getCurrentRankRegisterCnt($currentStamp = 0, $isRetain = 0)
    {
        $tableName = 'oibirthday.users';
        $currentDate = date('Y-m-d', $currentStamp);
        $loginEndDate = $this->connectObj->calculateLoginIn($currentStamp);
        $loginEndSting = "TO_DAYS(" . $loginEndDate . ")";
        $loginStartDate = $loginEndDate;
        if ($isRetain) {
            $loginStartDate = $this->connectObj->calculateLoginIn($currentStamp, $isRetain);   // 7  =>  6
        }
        $loginStartString = "TO_DAYS(" . $loginStartDate . ")";
        $sql = sprintf("
        SELECT 
	COUNT(*) AS cnt 
FROM 
	%s AS u 
WHERE 
	TO_DAYS(u.create_on) >= %s 
	AND TO_DAYS(u.create_on) <= %s
	",
            $tableName,
            $loginStartString,
            $loginEndSting);
        echo $sql . " \n";
        $query = $this->connectObj->fetchCnt($sql);
        return array(
            'create_on' => "'" . $currentDate . "'",
            'user_rank_cnt' => $query['cnt'],
        );

    }
}