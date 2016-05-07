<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/5
 * Time: 下午4:47
 */
include __DIR__ ."/middleware/CoreTaskQuery.php";
class MonthCoreTask extends CoreTaskQuery
{
    const MONTH_TABLE_NAME = 'user_core_task_retain_monthly_statis';
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    public function insertMonthTaskCnt($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $precedingMonth = clone $dateTime;
        $days = date('t', $precedingMonth->modify('-1 month')->getTimestamp());
        $isRetainDays = intval($days) - 1;
        $dateTime->modify('-1 day');
        $params = $this->fetchBirthLevelUsers($dateTime->getTimestamp(), $isRetainDays, 30);
        $paramsList = array(
            'create_on' => "'". $precedingMonth->format('Y-m-d') ."'",
            'month_core_task_cnt' => $params['birth_cnt_rank_0610'] + $params['birth_cnt_rank_1000'],
            'month_uncore_task_cnt' => $params['birth_cnt_rank_0000'] + $params['birth_cnt_rank_0001'] + $params['birth_cnt_rank_0205']
        );
        $insertSql = $this->common->insertParamsQuery(self::MONTH_TABLE_NAME, $paramsList);
        $query = $this->common->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $params['create_on'] . " month Insert " . self::MONTH_TABLE_NAME . " Success !!! \n";
        }
    }
}

$monthTask = new MonthCoreTask();
$monthTask->insertMonthTaskCnt();