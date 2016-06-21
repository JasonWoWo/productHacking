<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/17
 * Time: 下午4:15
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;


class SmsRegisterCoreQuery
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getSmsRegisters()
    {
        $smsToRegisters = array();
        $collection = $this->connectObj->fetchUnRegisterInfoCollection();
        $query = array(
            'register' => 1, 
        );
        $collectionItems = $collection->find($query);
        foreach ($collectionItems as $item) {
            if ($item['userId']) {
                $smsToRegisters[] = $item['userId'];
            }
        }
        return $smsToRegisters;
    }

    public function getSmsRegisterRetain($smsToRegisters = array(), $visitOnTimeStamp = 0)
    {
        $userIdString = implode(',', $smsToRegisters);
        $usersRetainCntQuery = $this->getQueryRegisterVisitCnt($userIdString, $visitOnTimeStamp);
        $result = $this->connectObj->fetchCnt($usersRetainCntQuery);
        return $result['cnt'];
    }
    
    public function getSmsRegisterCreate($smsToRegisters = array(), $createOnTimeStamp = 0)
    {
        $registers = array();
        $userIdString = implode(',', $smsToRegisters);
        $userCreateOnQuery = $this->getQueryRegisterByUserAndCreateOn($userIdString, $createOnTimeStamp);
        $result = $this->connectObj->fetchAssoc($userCreateOnQuery);
        foreach ($result as $item) {
            $registers[] = $item['id'];
        }
        return $registers;
    }
}