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
    const DAILY_PLATFORM_STAT = 'daily_platform_register_wechat_stat';

    const DAILY_BRANDS_STAT = 'daily_brand_register_wechat_stat';

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
        $brandList = $this->fetchBrandsCnt($userRegisterItems, $dateTime->getTimestamp());
        $brandList['create_on'] = "'" . $dateTime->format('Y-m-d') ."'";
        unset($brandList['iphone_cnt']);
        $insertSql = $this->common->insertParamsQuery(self::DAILY_BRANDS_STAT, $brandList);
        $query = $this->common->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $brandList['create_on'] . " Register Brand Category Insert " . self::DAILY_BRANDS_STAT . " Success !!! \n";
        }
        echo "== xiaomi_cnt: " . $brandList['xiaomi_cnt'] . " == meizu_cnt: " . $brandList['meizu_cnt'] . " == huawei_cnt: " . $brandList['huawei_cnt'] .
            " == vivo_cnt: " . $brandList['vivo_cnt'] . " == samsung_cnt: " . $brandList['samsung_cnt'] . " == oppo_cnt: " . $brandList['oppo_cnt'] .
            " == zte_cnt: " . $brandList['zte_cnt'] . " \n";
    }
    
    public function fetchRegisterProductList($extendTimeStamp = 0)
    {
        $params = array();
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $userRegisterItems = $this->getCurrentRankRegisterCnt($dateTime->getTimestamp(), 0, false);
        $params['iphone_register_cnt'] = $this->fetchProductListCnt($userRegisterItems, 1001, $dateTime->getTimestamp());
        $params['android_register_cnt'] = $this->fetchProductListCnt($userRegisterItems, 1002, $dateTime->getTimestamp());
        $params['create_on'] = "'" . $dateTime->format('Y-m-d') ."'";
        echo "=== iphone_register_cnt: " . $params['iphone_register_cnt'] . " === android_register_cnt: " . $params['android_register_cnt'] . " === create_on: " . $params['create_on'] . " \n";
        $insertSql = $this->common->insertParamsQuery(self::DAILY_PLATFORM_STAT, $params);
        $query = $this->common->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $params['create_on'] . " Register Platform Category Insert " . self::DAILY_PLATFORM_STAT . " Success !!! \n";
        }
    }
}
$userBrandList = new UserDailyBrandStat();
$userBrandList->fetchRegisterBrandList();
$userBrandList->fetchRegisterProductList();