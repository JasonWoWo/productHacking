<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午3:09
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserRegisterRetainQuery;

class UserDailyRegisterRetain extends UserRegisterRetainQuery
{
    const USER_TABLE_DAILY_NAME = 'user_retain_daily_statis';

    public function userDailyRetainUpdate($extendStamp = 0)
    {
        echo "========== userDailyRetainUpdate start ===== \n";
        $this->updateBaseRegisterRetainCnt($extendStamp, 2);
        $this->updateBaseRegisterRetainCnt($extendStamp, 3);
        $this->updateBaseRegisterRetainCnt($extendStamp, 4);
        $this->updateBaseRegisterRetainCnt($extendStamp, 5);
        $this->updateBaseRegisterRetainCnt($extendStamp, 6);
        $this->updateBaseRegisterRetainCnt($extendStamp, 7);
        $this->updateBaseRegisterRetainCnt($extendStamp, 15);
        $this->updateBaseRegisterRetainCnt($extendStamp, 30);
        echo "========== userDailyRetainUpdate end ===== \n";
    }

    // 每天执行注册用户的留存数据
    public function updateBaseRegisterRetainCnt($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = date('Y-m-d', $timestamp);
        $params = $this->getCurrentRankRegisterRetainCnt($timestamp, $isRetain);
        $paramKey = $this->getIsRetainDailyParamsKey($isRetain);
        $secondCnt = array(
            $paramKey => $params,
        );
        $loginIn = $this->connectObj->calculateLoginIn($timestamp, $isRetain);
        if ($this->checkCurrentDateData(self::USER_TABLE_DAILY_NAME, $loginIn)) {
            $where = array('create_on' => $loginIn);
            $updateQuery = $this->connectObj->updateParamsQuery(self::USER_TABLE_DAILY_NAME, $secondCnt, $where);
            $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
            if ($query) {
                echo " === " . $currentDate . " week isRetain : " . $isRetain . " success !!! \n";
            }
        }
    }
}
$userDailyRegisterRetain = new UserDailyRegisterRetain();
$userDailyRegisterRetain->userDailyRetainUpdate();