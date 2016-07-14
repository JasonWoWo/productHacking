<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/14
 * Time: 上午11:57
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\ActivationQuery;
class Activation extends ActivationQuery
{
    const AWAKEN_USER_RETAIN = 'user_awaken_retain';

    public function updateActivationUsers($extendStamp = 0)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $pointDate = new \DateTime(date('Y-m-d', $timeStamp));
        $this->getActivationSendUsers($pointDate);
    }
    
    public function insertAwakenUsers($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $currentDate = $dateTime->format('Y-m-d');
        $param['create_on'] = "'" . $currentDate . "'";
        $param['user_awaken_cnt'] = count($this->activeUsers);
        $insertSql = $this->connectObj->insertParamsQuery(self::AWAKEN_USER_RETAIN, $param);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $currentDate . " Insert " . self::AWAKEN_USER_RETAIN ." Success !!! \n";
        }
    }

    public function baseActivationRetain($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = new \DateTime(date('Y-m-d', $timestamp));

        $paramsId = array('id');
        $cloneCurrent = clone $currentDate;
        $where = array('create_on' => "'" . $currentDate->modify("-{$isRetain} days")->format('Y-m-d') ."'");
        $selectQuery = $this->connectObj->selectParamsQuery(self::AWAKEN_USER_RETAIN, $paramsId, $where);
        $result = $this->connectObj->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::AWAKEN_USER_RETAIN . " \n";
            return ;
        }
        $awaken = $this->fetchAwakenUserId($currentDate);
        $retainCnt = $this->getAwakenUserRetainCnt($awaken, $cloneCurrent->modify('-1 day')->getTimestamp());
        $params = array($this->getSmsRetainParamKey($isRetain) => $retainCnt);
        $updateQuery = $this->connectObj->updateParamsQuery(self::AWAKEN_USER_RETAIN, $params, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo " ===" . $where['create_on'] ." Update " . self::AWAKEN_USER_RETAIN . " success !!! \n";
        }
    }
}
$awakenPlan = new Activation();
$awakenPlan->getUserDetails(2443261);