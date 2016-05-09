<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午2:44
 */
include __DIR__ . "/middleware/UserRegisterQuery.php";
class UserDailyRegister extends UserRegisterQuery
{
    const USER_TABLE_DAILY_NAME = 'user_retain_daily_statis';
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }

    // 每天完成注册用户数据
    public function insertCurrentUserDaily($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $params = $this->getCurrentRankRegisterCnt($dateTime->getTimestamp());
        $dailyParams = array(
            'create_on' => $params['create_on'],
            'user_cnt' => $params['user_rank_cnt']
        );
        $insertSql = $this->common->insertParamsQuery(self::USER_TABLE_DAILY_NAME, $dailyParams);
        $query = $this->common->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $dailyParams['create_on'] . " Daily Insert " . self::USER_TABLE_DAILY_NAME . " Success !!! \n";
        }
    }
}
$userDailyRegister = new UserDailyRegister();
$userDailyRegister->insertCurrentUserDaily();