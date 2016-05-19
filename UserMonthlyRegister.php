<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午3:03
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserRegisterQuery;

class UserMonthlyRegister extends UserRegisterQuery
{
    const USER_TABLE_MONTHLY_NAME = 'user_retain_monthly_statis';

    // 每月1日执行上月完成注册用户数据
    public function insertCurrentMonthRegisterCnt($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $precedingMonth = clone $dateTime;
        $days = date('t', $precedingMonth->modify('-1 month')->getTimestamp());
        $isRetainDays = intval($days);
        $dateTime->modify('-1 day');
        $params = $this->getCurrentRankRegisterCnt($dateTime->getTimestamp(), $isRetainDays);
        $monthParams = array(
            'create_on' => "'" . $precedingMonth->format('Y-m-d') ."'",
            'month_user_cnt' => $params['user_rank_cnt'],
        );
        $insertSql = $this->connectObj->insertParamsQuery(self::USER_TABLE_MONTHLY_NAME, $monthParams);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $monthParams['create_on'] . " month Insert " . self::USER_TABLE_MONTHLY_NAME . " Success !!! \n";
        }
    }
}
$userMonthRegister = new UserMonthlyRegister();
$userMonthRegister->insertCurrentMonthRegisterCnt();