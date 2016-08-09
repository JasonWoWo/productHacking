<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/9
 * Time: 下午2:55
 */

require __DIR__ . '/Bootstrap.php';
use MiddlewareSpace\UnCoreUseAuthorize;

class DailyCoreTask extends UnCoreUseAuthorize
{
    const USER_CORE_TASK_RETAIN_TABLE = 'user_core_task_retain_statis';

    public function insertDailyCoreTaskCnt($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify('-1 day');
        $this->getUnCoreUser($dateTime->getTimestamp(), 0, -1, 5);
        $paramsList = array(
            'create_on' => "'". $dateTime->format('Y-m-d') ."'",
            'core_task_cnt' => 0,
            'uncore_task_cnt' => count($this->userDetailItem),
        );
        $this->userDetailItem = array();
        $this->getUnCoreUser($dateTime->getTimestamp(), 0, 6, 1000);
        $paramsList['core_task_cnt'] = count($this->userDetailItem);
        $insertSql = $this->connectObj->insertParamsQuery(self::USER_CORE_TASK_RETAIN_TABLE, $paramsList);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $paramsList['create_on'] . " daily Insert " . self::USER_CORE_TASK_RETAIN_TABLE . " Success !!! \n";
        }
    }
}
$daily = new DailyCoreTask();
$daily->insertDailyCoreTaskCnt();