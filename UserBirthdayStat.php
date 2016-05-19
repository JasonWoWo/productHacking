<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/10
 * Time: 下午6:24
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\BirthdayRegisterQuery;

class UserBirthdayStat extends BirthdayRegisterQuery
{
    const DEFAULT_USER_MAX_COUNT = 50000;

    const PLATFORM_REMINDER_STAT = 'stat_platform_reminder_statis';

    public function getPointUserCnt($productSk = 1002)
    {
        $maxUserId = $this->getMaxUserId();
        $birthdayTableCnt = intval($maxUserId / self::DEFAULT_USER_MAX_COUNT);
        $defaultTable = 0;
        $total = 0;
        while ($defaultTable <= $birthdayTableCnt) {
            $total += $this->getPointDayBirthdayUserCnt($defaultTable, $productSk);
            $defaultTable = $defaultTable + 1;
        }
        echo  "Summation product_sk " . $productSk . " On 2016-05-11 : " . $total . " \n";
        return $total;
    }

    public function productList()
    {
        $paramsId = array('id');
        $current = new \DateTime();
        $where = array('create_on' => "'" . $current->modify('-1 day')->format('Y-m-d') ."'");
        $selectQuery = $this->connectObj->selectParamsQuery(self::PLATFORM_REMINDER_STAT, $paramsId, $where);
        $result = $this->connectObj->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::PLATFORM_REMINDER_STAT . " \n";
            return ;
        }
        $params = array(
            'current_iphone_reminder_cnt' => 0,
            'current_android_reminder_cnt' => 0
        );
        $params['current_iphone_reminder_cnt'] = $this->getPointUserCnt(1001);
        $params['current_android_reminder_cnt'] = $this->getPointUserCnt(1002);

        $updateQuery = $this->connectObj->updateParamsQuery(self::PLATFORM_REMINDER_STAT, $params, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo " ===UserBrandBirthdayStat " . $current->format('Y-m-d') . " Update brands success !!! \n";
        }

    }
}
$userBirthday = new UserBirthdayStat();
$userBirthday->productList();