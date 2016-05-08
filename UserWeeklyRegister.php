<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午2:51
 */
include __DIR__ . "/middleware/UserRegisterQuery.php";
class UserWeeklyRegister extends UserRegisterQuery
{
    const USER_TABLE_WEEKLY_NAME = 'user_retain_weekly_statis';

    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    public function insertCurrentWeekRegisterCnt($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $params = $this->getCurrentRankRegisterCnt($dateTime->getTimestamp(), 7);
        $weekParams = array(
            'create_on' => "'" . $dateTime->modify('-1 day')->format('Y-m-d') ."'",
            'week_user_cnt' => $params['user_rank_cnt'],
        );
        $insertSql = $this->common->insertParamsQuery(self::USER_TABLE_WEEKLY_NAME, $weekParams);
        $query = $this->common->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $params['create_on'] . " week Insert " . self::USER_TABLE_WEEKLY_NAME . " Success !!! \n";
        }
    }
}
$userWeekRegister = new UserWeeklyRegister();
$userWeekRegister->insertCurrentWeekRegisterCnt();