<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/11
 * Time: 下午3:20
 */
include __DIR__ . "/middleware/BirthdayRegisterQuery.php";
class UserBrandBirthdayStat extends BirthdayRegisterQuery
{
    const DEFAULT_USER_MAX_COUNT = 50000;

    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    public function getPointBrandUserCnt()
    {
        $summationBrandCnt = array(
            'iphone_cnt' => 0,
            'meizu_cnt' => 0,
            'huawei_cnt' => 0,
            'vivo_cnt' => 0,
            'samsung_cnt' => 0,
            'oppo_cnt' => 0,
            'zte_cnt' => 0
        );
        $maxUserId = $this->getMaxUserId();
        $birthdayTableCnt = intval($maxUserId / self::DEFAULT_USER_MAX_COUNT);
        $defaultTable = 0;
        while ($defaultTable <= $birthdayTableCnt) {
            $currentBrandCnt = $this->getPointDayBrandBirthdayUserCnt($defaultTable);
            $summationBrandCnt = $this->summationDeviceCnt($summationBrandCnt, $currentBrandCnt);
            $defaultTable += 1;
        }
        echo "== iphone_cnt: " . $summationBrandCnt['iphone_cnt'] . " == meizu_cnt: " . $summationBrandCnt['meizu_cnt'] . " == huawei_cnt: " . $summationBrandCnt['huawei_cnt'] .
            " == vivo_cnt: " . $summationBrandCnt['vivo_cnt'] . " == samsung_cnt: " . $summationBrandCnt['samsung_cnt'] . " == oppo_cnt: " . $summationBrandCnt['oppo_cnt'] .
            " == zte_cnt: " . $summationBrandCnt['zte_cnt'] . " \n";
    }
}
$userBrandBirthdayStat = new UserBrandBirthdayStat();
$userBrandBirthdayStat->getPointBrandUserCnt();