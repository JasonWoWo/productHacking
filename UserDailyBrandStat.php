<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/13
 * Time: 下午6:10
 */
include __DIR__ . "/middleware/UserRegisterQuery.php";
class UserDailyBrandStat extends UserRegisterQuery
{
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }
    
    public function fetchRegisterBrandList($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $userRegisterItems = $this->getCurrentRankRegisterCnt($dateTime->getTimestamp(), 0, false);
        $brandList = $this->fetchBrandsCnt($userRegisterItems);
        echo "== xiaomi_cnt: " . $brandList['xiaomi_cnt'] . " == meizu_cnt: " . $brandList['meizu_cnt'] . " == huawei_cnt: " . $brandList['huawei_cnt'] .
            " == vivo_cnt: " . $brandList['vivo_cnt'] . " == samsung_cnt: " . $brandList['samsung_cnt'] . " == oppo_cnt: " . $brandList['oppo_cnt'] .
            " == zte_cnt: " . $brandList['zte_cnt'] . " \n";
    }
}
$userBrandList = new UserDailyBrandStat();
$userBrandList->fetchRegisterBrandList();