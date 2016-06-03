<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/3
 * Time: 下午1:43
 */
use MiddlewareSpace\CoreTaskQuery;

class RegisterInformation extends CoreTaskQuery
{
    const USER_PROMOTION_TABLE_NAME = 'user_promotion_statis';

    public function getWeekCompleteInfo($extendStamp = 0, $isRetain = 7)
    {
        $extendStamp = empty($extendStamp) ? time() : $extendStamp;
        $pointStamp = $extendStamp - $isRetain * 86400;
        $params = array('id');
        $where = array('create_on' => "'" . date('Y-m-d', $pointStamp) ."'");
        $selectQuery = $this->connectObj->selectParamsQuery(self::USER_PROMOTION_TABLE_NAME, $params, $where);
        $result = $this->connectObj->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::USER_PROMOTION_TABLE_NAME . " \n";
            return ;
        }
        $weekCompleteCnt = $this->getRegisterCompleteCount($pointStamp);
        $param['week_complete_info_cnt'] = $weekCompleteCnt;
        $updateQuery = $this->connectObj->updateParamsQuery(self::USER_PROMOTION_TABLE_NAME, $param, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo self::USER_PROMOTION_TABLE_NAME . "Update where create_on: " . $where['create_on'] . " Success ! \n";
        }
    }

    public function getMonthCompleteInfo($extendStamp = 0, $isRetain = 30)
    {
        $extendStamp = empty($extendStamp) ? time() : $extendStamp;
        $pointStamp = $extendStamp - $isRetain * 86400;
        $params = array('id');
        $where = array('create_on' => "'" . date('Y-m-d', $pointStamp) ."'");
        $selectQuery = $this->connectObj->selectParamsQuery(self::USER_PROMOTION_TABLE_NAME, $params, $where);
        $result = $this->connectObj->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::USER_PROMOTION_TABLE_NAME . " \n";
            return ;
        }
        $monthCompleteCnt = $this->getRegisterCompleteCount($pointStamp);
        $param['month_complete_info_cnt'] = $monthCompleteCnt;
        $updateQuery = $this->connectObj->updateParamsQuery(self::USER_PROMOTION_TABLE_NAME, $param, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo self::USER_PROMOTION_TABLE_NAME . "Update where create_on: " . $where['create_on'] . " Success ! \n";
        }
    }
    
    public function main()
    {
        $currentDate = new \DateTime('2016-06-01');
        $endDate = new \DateTime('2016-04-01');
        while ($currentDate->getTimestamp() >= $endDate->getTimestamp()) {
            $currentStamp = $currentDate->getTimestamp();
            $this->getWeekCompleteInfo($currentStamp);
            $this->getMonthCompleteInfo($currentStamp);
            $currentDate->modify('-1 day');
        }
    }
}
$infoFix = new RegisterInformation();
$infoFix->main();