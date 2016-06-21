<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/21
 * Time: 下午1:31
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class ExchangerQuery
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;
    
    public $exchangers = array();

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getDailyRegisters(\DateTime $pointDate)
    {
        $defaultTable = 0;
        while ($defaultTable < 8) {
            $this->getDeviceRegisters($defaultTable, $pointDate->getTimestamp());
            $defaultTable += 1;
        }
    }

    public function getDeviceRegisters($tableNum = 0, $loginStamp = 0)
    {
        $oiDevice = 'oistatistics.st_devices_' . $tableNum;
        $userQuery = $this->getQueryRankUserId($oiDevice, $loginStamp, $loginStamp, true);
        $registers = $this->connectObj->fetchAssoc($userQuery);
        foreach ($registers as $register) {
            $userId = $register['id'];
            $consumeCnt = $this->getRegisterConsumeOnExchange($userId);
            if ($consumeCnt) {
                $this->exchangers[] = $register;
            }
        }
    }

    public function getRegisterConsumeOnExchange($userId)
    {
        $consumeQuery = $this->getQueryFirstExchangeRegister($userId);
        $result = $this->connectObj->fetchAssoc($consumeQuery);
        return $result['consumeCnt'];
    }
    
    public function getExtraDetail()
    {
        foreach ($this->exchangers as &$item) {
            $item['authorize'] = $this->getContactAuthorize($item['udid']);
            $item['backUpBirths'] = $this->getBirthCntDetail($item['id']);
        }
    }
    
    public function getContactAuthorize($udid)
    {
        $collection = $this->connectObj->fetchDeviceInfoCollection();
        $query = array('_id' => $udid);
        $authorizeDetail = $collection->findOne($query);
        $authorize = -1;
        if ($authorizeDetail) {
            $authorize = $authorizeDetail['combineAuthorizeStatus'];
        }
        return $authorize;
    }
    
    public function getBirthCntDetail($userId)
    {
        $number = $this->get_number_birthday_number($userId);
        $userBackUpBirthQuery = $this->getQueryBackUpBirthDetail($number, $userId);
        $result = $this->connectObj->fetchCnt($userBackUpBirthQuery);
        return $result['cnt'];
    }

}