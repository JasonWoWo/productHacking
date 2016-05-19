<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/11
 * Time: 下午3:20
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\BirthdayRegisterQuery;

class UserBrandBirthdayStat extends BirthdayRegisterQuery
{
    const DEFAULT_USER_MAX_COUNT = 50000;

    const BRAND_DAILY_REMINDER_STAT = 'stat_daily_brand_reminder_statis';

    public function getPointBrandUserCnt()
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
        $params = array('id');
        $current = new \DateTime();
        $where = array('create_on' => "'" . $current->modify('-1 day')->format('Y-m-d') ."'");
        $selectQuery = $this->connectObj->selectParamsQuery(self::BRAND_DAILY_REMINDER_STAT, $params, $where);
        $result = $this->connectObj->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::BRAND_DAILY_REMINDER_STAT . " \n";
            return ;
        }
        $maxUserId = $this->getMaxUserId();
        $birthdayTableCnt = intval($maxUserId / self::DEFAULT_USER_MAX_COUNT);
        $defaultTable = 0;
        while ($defaultTable <= $birthdayTableCnt) {
            $currentBrandCnt = $this->getPointDayBrandBirthdayUserCnt($defaultTable);
            $summationBrandCnt = $this->summationDeviceCnt($summationBrandCnt, $currentBrandCnt);
            $defaultTable += 1;
        }
        unset($summationBrandCnt['iphone_cnt']);
        $updateQuery = $this->connectObj->updateParamsQuery(self::BRAND_DAILY_REMINDER_STAT, $summationBrandCnt, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo " ===UserBrandBirthdayStat " . $current->format('Y-m-d') . " Update brands success !!! \n";
        }
        echo "== xiaomi_cnt: " . $summationBrandCnt['xiaomi_cnt'] . " == meizu_cnt: " . $summationBrandCnt['meizu_cnt'] . " == huawei_cnt: " . $summationBrandCnt['huawei_cnt'] .
            " == vivo_cnt: " . $summationBrandCnt['vivo_cnt'] . " == samsung_cnt: " . $summationBrandCnt['samsung_cnt'] . " == oppo_cnt: " . $summationBrandCnt['oppo_cnt'] .
            " == zte_cnt: " . $summationBrandCnt['zte_cnt'] . " \n";
    }
}
$userBrandBirthdayStat = new UserBrandBirthdayStat();
$userBrandBirthdayStat->getPointBrandUserCnt();