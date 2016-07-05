<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/5
 * Time: 下午5:49
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\AddBirthUpQuery;
class UserBirthSummation extends AddBirthUpQuery
{
    const DAILY_ALL_ADD_USER_SUMMATION = 'daily_all_user_summation';

    public function getSummationBirthCnt($extendStamp = 0)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify('-1 day');
        $insertParamKey = $this->getSrcParamInsertKey();
        $srcValue = $this->getUserAddBirthCnt($dateTime);
        $params['create_on'] = "'{$dateTime->format('Y-m-d')}'";
        foreach ($srcValue as $key => $value) {
            $insertSrcItem = $insertParamKey['srcItem'];
            $params[$insertSrcItem[$key]] = $value;
            echo "==== src: " . $key . " ==== value: " . $value . " ==== \n";
        }
        $insertSql = $this->connectObj->insertParamsQuery(self::DAILY_ALL_ADD_USER_SUMMATION, $params);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $params['create_on'] . " Insert " . self::DAILY_ALL_ADD_USER_SUMMATION ." Success !!! \n";
        }
    }
}
$summation = new UserBirthSummation();
$summation->getSummationBirthCnt();