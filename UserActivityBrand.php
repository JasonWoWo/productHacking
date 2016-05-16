<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/16
 * Time: 上午10:54
 */
include __DIR__ . "/middleware/BirthdayRegisterQuery.php";
class UserActivityBrand extends BirthdayRegisterQuery
{
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }
    
    public function fetchBrandsActivities()
    {
        $summationBrandCnt = array(
            'iphone_cnt' => 0,
            'xiaomi_cnt' =>0,
            'meizu_cnt' => 0,
            'huawei_cnt' => 0,
            'vivo_cnt' => 0,
            'samsung_cnt' => 0,
            'oppo_cnt' => 0,
            'zte_cnt' => 0
        );
        $currentSummation = $this->getPointUserBrandsCnt();
        $summationBrandCnt = $this->summationDeviceCnt($summationBrandCnt, $currentSummation);
        echo "== xiaomi_cnt: " . $summationBrandCnt['xiaomi_cnt'] . " == meizu_cnt: " . $summationBrandCnt['meizu_cnt'] . " == huawei_cnt: " . $summationBrandCnt['huawei_cnt'] .
            " == vivo_cnt: " . $summationBrandCnt['vivo_cnt'] . " == samsung_cnt: " . $summationBrandCnt['samsung_cnt'] . " == oppo_cnt: " . $summationBrandCnt['oppo_cnt'] .
            " == zte_cnt: " . $summationBrandCnt['zte_cnt'] . " \n";
    }
}
$userActivity = new UserActivityBrand();
$userActivity->fetchBrandsActivities();