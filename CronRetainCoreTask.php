<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/5
 * Time: 下午3:23
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\CoreTaskQuery;

class CronRetainCoreTask extends CoreTaskQuery
{
    /**
     * 核心任务的基础数据 - 核心任务的留存数据
     */

    const USER_PROMOTION_TABLE_NAME = 'user_promotion_statis';

    // 更新7日和30日的核心用户数量
    public function updateCoreTaskPromotion($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $currentDate = new \DateTime(date('Y-m-d', $timeStamp));
        $cloneCurrentDate = clone $currentDate;
        $weekParams = $this->totalDeviceRetain($timeStamp,7);
        $weekCnt = array(
            'week_core_task_cnt' => $weekParams['retainCTCnt'],
        );
        $currentDate->modify('-7 days');
        $weekParamsWhere = array(
            'create_on' => "'" . $currentDate->format('Y-m-d') . "'",
        );
        $updateQuery = $this->connectObj->updateParamsQuery(self::USER_PROMOTION_TABLE_NAME, $weekCnt, $weekParamsWhere);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo "==== " . $weekParamsWhere['create_on'] . " Update " . self::USER_PROMOTION_TABLE_NAME ." Retain 7 Success !!! \n";
        }
        $monthParams = $this->totalDeviceRetain($timeStamp, 30);
        $monthCnt = array(
            'month_core_task_cnt' => $monthParams['retainCTCnt'],
        );
        $cloneCurrentDate->modify('-30 days');
        $monthParamsWhere = array(
            'create_on' => "'" . $cloneCurrentDate->format('Y-m-d') . "'",
        );
        $updateQuery = $this->connectObj->updateParamsQuery(self::USER_PROMOTION_TABLE_NAME, $monthCnt, $monthParamsWhere);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo "==== " . $weekParamsWhere['create_on'] . " Update " . self::USER_PROMOTION_TABLE_NAME ." Retain 30 Success !!! \n";
        }
    }
}
$cron = new CronRetainCoreTask();
$cron->updateCoreTaskPromotion();