<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午3:10
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\UserRegisterRetainQuery;

class UserMonthlyRegisterRetain extends UserRegisterRetainQuery
{
    const USER_TABLE_MONTHLY_NAME = 'user_retain_monthly_statis';

    public function updateMonthRegisterRetainCnt()
    {
        $this->updateBaseRankRegisterRetainCnt(2);
        $this->updateBaseRankRegisterRetainCnt(3);
        $this->updateBaseRankRegisterRetainCnt(4);
        $this->updateBaseRankRegisterRetainCnt(5);
        $this->updateBaseRankRegisterRetainCnt(6);
        $this->updateBaseRankRegisterRetainCnt(7);
        $this->updateBaseRankRegisterRetainCnt(8);
        $this->updateBaseRankRegisterRetainCnt(9);
        $this->updateBaseRankRegisterRetainCnt(10);
    }
    
    // 每天执行月注册用户的留存数据
    public function updateBaseRankRegisterRetainCnt($isRetainMonth = 0)
    {
        $current = new \DateTime();
        $nextMonth = $current->modify("+1 month");
        $date = new \DateTime(date('Y-m-01', $nextMonth->getTimestamp()));
        $pointDate = clone $date;
        $visitDate = clone $date;
        $pointDate->modify("-$isRetainMonth months");
        $visitDate->modify("-1 month");
        $paramCnt = $this->getCurrentRankMonthRegisterRetainCnt($pointDate, $visitDate);
        $paramKey = $this->getIsRetainMonthParamsKey($isRetainMonth);
        $updateParams = array(
            $paramKey => $paramCnt,
        );
        $monthLabelString = "'" . $pointDate->format('Y-m-d') . "'";
        if ($this->checkCurrentDateData(self::USER_TABLE_MONTHLY_NAME, $monthLabelString)) {
            $where = array('create_on' => $monthLabelString);
            $updateQuery = $this->connectObj->updateParamsQuery(self::USER_TABLE_MONTHLY_NAME, $updateParams, $where);
            $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
            if ($query) {
                echo " === " . $monthLabelString . " month isRetain : " . $isRetainMonth . " success !!! \n";
            }
        }
    }
}
$monthRetain = new UserMonthlyRegisterRetain();
$monthRetain->updateMonthRegisterRetainCnt();
