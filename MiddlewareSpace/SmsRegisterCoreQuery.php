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

    public function getSmsRegisters(\DateTime $yesterday)
    {
        $smsToRegisters = array();
        $collection = $this->connectObj->fetchUnRegisterInfoCollection();
        $query = array(
            'register' => 1, 
            'lastUpdated' => array('$gte' => $yesterday, '$lte' => $yesterday->modify('+1 day'))
        );
        $collectionItems = $collection->find($query);
        foreach ($collectionItems as $item) {
            $smsToRegisters[] = $item['userId'];
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
}