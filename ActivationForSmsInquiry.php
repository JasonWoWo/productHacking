<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/17
 * Time: 下午1:58
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\ActivationQuery;

class ActivationForSmsInquiry extends ActivationQuery
{
    const USER_INQUIRY_RETAIN_STAT = 'user_inquiry_sms_retain_cnt';
    
    public function getInquirySmsUsers($extendStamp = 0)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $pointDate = new \DateTime(date('Y-m-d', $timeStamp));
        $this->getInquiryAwakenUsers($pointDate);
    }

    public function insertInquiryAwakenUsers($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $currentDate = $dateTime->format('Y-m-d');
        $param['create_on'] = "'" . $currentDate . "'";
        $param['user_inquiry_cnt'] = count($this->inquiryActivityUser);
        $insertSql = $this->connectObj->insertParamsQuery(self::USER_INQUIRY_RETAIN_STAT, $param);
//        $result = $this->connectObj->fetchCakeStatQuery($insertSql);
//        if ($result) {
//            echo "==== " . $currentDate . " Insert " . self::USER_INQUIRY_RETAIN_STAT ." Success !!! \n";
//        }

    }

    public function updateRetain($extendStamp = 0)
    {
        $this->baseInquiryRetain($extendStamp , 2);
        $this->baseInquiryRetain($extendStamp , 3);
        $this->baseInquiryRetain($extendStamp , 7);
        $this->baseInquiryRetain($extendStamp , 15);
        $this->baseInquiryRetain($extendStamp , 30);
    }

    public function baseInquiryRetain($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = new \DateTime(date('Y-m-d', $timestamp));

        $paramsId = array('id');
        $cloneCurrent = clone $currentDate;
        $where = array('create_on' => "'" . $currentDate->modify("-{$isRetain} days")->format('Y-m-d') ."'");
        $selectQuery = $this->connectObj->selectParamsQuery(self::USER_INQUIRY_RETAIN_STAT, $paramsId, $where);
        $result = $this->connectObj->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::USER_INQUIRY_RETAIN_STAT . " \n";
            return ;
        }
        $inquirys = $this->fetchInquiryUserId($currentDate);
        $retainCnt = $this->getAwakenUserRetainCnt($inquirys, $cloneCurrent->modify('-1 day')->getTimestamp());
        $params = array($this->getSmsRetainParamKey($isRetain) => $retainCnt);
        $updateQuery = $this->connectObj->updateParamsQuery(self::USER_INQUIRY_RETAIN_STAT, $params, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo " ===" . $where['create_on'] ." Update " . self::USER_INQUIRY_RETAIN_STAT . " success !!! \n";
        }
    }
}
$inquiry = new ActivationForSmsInquiry();
$inquiry->getInquirySmsUsers();
$inquiry->insertInquiryAwakenUsers();