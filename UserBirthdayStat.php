<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/10
 * Time: 下午6:24
 */
include __DIR__ . "/middleware/BirthdayRegisterQuery.php";
class UserBirthdayStat extends BirthdayRegisterQuery
{
    const DEFAULT_USER_MAX_COUNT = 50000;

    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

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
        echo  "Summation On 2016-05-10 : " . $total . " \n";
    }
}
$userBirthday = new UserBirthdayStat();
$userBirthday->getPointUserCnt();