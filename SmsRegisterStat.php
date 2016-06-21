<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/17
 * Time: 下午4:14
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\SmsRegisterCoreQuery;

class SmsRegisterStat extends SmsRegisterCoreQuery
{
    const SMS_REGISTER_STAT = 'sms_register_statis';

    public function insertSmsRegisterCnt($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $currentDate = $dateTime->format('Y-m-d');
        $params['create_on'] = "'" . $currentDate . "'";
        $userItems = $this->getSmsRegisters();
        $registers = $this->getSmsRegisterCreate($userItems, $dateTime->getTimestamp());
        $param = array('sms_registers_cnt' => count($registers));
        $insertSql = $this->connectObj->insertParamsQuery(self::SMS_REGISTER_STAT, $param);
        $query = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($query) {
            echo "==== " . $currentDate . " Insert " . self::SMS_REGISTER_STAT ." Success !!! \n";
        }
    }
    
    public function getRetainForSmsRegister($isRetain = 3)
    {
        $paramsId = array('id');
        $current = new \DateTime(date('Y-m-d'));
        $cloneCurrent = clone $current;
        $where = array('create_on' => "'" . $current->modify("-{$isRetain} days")->format('Y-m-d') ."'");
        $selectQuery = $this->connectObj->selectParamsQuery(self::SMS_REGISTER_STAT, $paramsId, $where);
        $result = $this->connectObj->fetchAssoc($selectQuery);
        if (empty($result)) {
            echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::SMS_REGISTER_STAT . " \n";
            return ;
        }
        $userItems = $this->getSmsRegisters();
        $registers = $this->getSmsRegisterCreate($userItems, $current->getTimestamp());
        $cnt = $this->getSmsRegisterRetain($registers, $cloneCurrent->getTimestamp());
        $params = array($this->getSmsRetainParamKey($isRetain) => $cnt);
        $updateQuery = $this->connectObj->updateParamsQuery(self::SMS_REGISTER_STAT, $params, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo " ===" . $where['create_on'] ." Update " . self::SMS_REGISTER_STAT . " success !!! \n";
        }
    }

    public function updateSmsRegisterRetain()
    {
        $this->getRetainForSmsRegister(2);
        $this->getRetainForSmsRegister(3);
        $this->getRetainForSmsRegister(7);
        $this->getRetainForSmsRegister(15);
        $this->getRetainForSmsRegister(30);
    }
}
$smsRegister = new SmsRegisterStat();
$smsRegister->insertSmsRegisterCnt();
$smsRegister->updateSmsRegisterRetain();