<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午3:10
 */
include __DIR__ . "/middleware/UserRegisterRetainQuery.php";
class UserWeeklyRegisterRetain extends UserRegisterRetainQuery
{
    const USER_TABLE_WEEKLY_NAME = 'user_retain_weekly_statis';

    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    public function updateWeekRegisterRetainCnt($extendStamp = 0)
    {
        echo "============ updateWeekRegisterRetainCnt => start \n";
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 7);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 14);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 21);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 28);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 35);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 42);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 49);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 56);
        $this->updateBaseRankRegisterRetainCnt($extendStamp, 63);
        echo "============ updateWeekRegisterRetainCnt => end \n";

    }
    
    public function updateBaseRankRegisterRetainCnt($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = new \DateTime(date('Y-m-d', $timestamp));
        $nextDate = clone $currentDate;
        $weekdays = date("N", $currentDate->getTimestamp());
        $nextWeekDistance = 8 - $weekdays;
        $nextDate->modify("+$nextWeekDistance days");
        $paramCnt = $this->getCurrentRankRegisterRetainCnt($nextDate->getTimestamp(), $isRetain, 7);
        $paramKey = $this->getIsRetainWeekParamsKey($isRetain);
        $updateParams = array(
            $paramKey => $paramCnt,
        );
        
        $distance = $isRetain + 1;
        $loginIn = $nextDate->modify("-$distance days")->format('Y-m-d');
        $loginInString = "'" . $loginIn . "'";
        if ($this->checkCurrentDateData(self::USER_TABLE_WEEKLY_NAME, $loginIn)) {
            $where = array('create_on' => $loginInString);
            $updateQuery = $this->common->updateParamsQuery(self::USER_TABLE_WEEKLY_NAME, $updateParams, $where);
            $query = $this->common->fetchCakeStatQuery($updateQuery);
            if ($query) {
                echo " === " . $loginInString . " week isRetain : " . $isRetain . " success !!! \n";
            }
        }
    }
}
$userWeekRegisterRetain = new UserWeeklyRegisterRetain();
$userWeekRegisterRetain->updateWeekRegisterRetainCnt();