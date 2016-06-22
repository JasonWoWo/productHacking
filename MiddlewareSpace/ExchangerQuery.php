<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/21
 * Time: 下午1:31
 */

namespace MiddlewareSpace;

use BaseSpace\baseController;


class ExchangerQuery extends baseController
{
    public $exchangers = array();

    public function __construct()
    {
        parent::__construct();
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
            $consumeCnt = $this->getRegisterConsumeOnExchange($userId, $loginStamp);
            if ($consumeCnt) {
                $this->exchangers[] = $register;
            }
        }
    }

    public function getRegisterConsumeOnExchange($userId, $consumeStamp = 0)
    {
        $consumeQuery = $this->getQueryFirstExchangeRegister($userId, $consumeStamp);
        $result = $this->connectObj->fetchCnt($consumeQuery);
        return $result['consumeCnt'];
    }
    
    public function getExtraDetail()
    {
        foreach ($this->exchangers as &$item) {
            $item['authorize'] = $this->getContactAuthorize($item['udid']);
            $item['backUpBirths'] = $this->getBirthCntDetail($item['id']);
        }
    }
    
}