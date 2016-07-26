<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/17
 * Time: 下午4:15
 */

namespace MiddlewareSpace;

use BaseSpace\baseController;



class SmsRegisterCoreQuery extends baseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSmsRegisters()
    {
        $smsToRegisters = array();
        $collection = $this->connectObj->fetchUnRegisterInfoCollection();
        $query = array(
            'register' => 1,
            'click' => array('$gte' => 1)
        );
        $collectionItems = $collection->find($query);
        foreach ($collectionItems as $item) {
            if ($item['userId']) {
                $smsToRegisters[] = $item['userId'];
            }
        }
        return $smsToRegisters;
    }

    public function getSmsRegisterOnBatch($batchNo = 0, $task = 0)
    {
        $taskRegisters = array();
        $collection = $this->connectObj->fetchUnRegisterInfoCollection();
        $query = array(
            'batch_no' => $batchNo,
            'register' => 1,
            'task' => array('$in' => array($task)),
            'click' => array('$gte' => 1)
        );
        $collectionItems = $collection->find($query);
        foreach ($collectionItems as $item) {
            if ($item['userId']) {
                $taskRegisters[] = $item['userId'];
            }
        }
        return $taskRegisters;
    }
    
    public function getRegisterDetailStatus($taskRegisters = array())
    {
        $registerList = implode(',', $taskRegisters);
        echo $registerList . " \n";
        $detailItemsQuery = $this->getQueryRegisterBirthDetail($registerList);
        $detailItems = $this->connectObj->fetchAssoc($detailItemsQuery);
        foreach ($detailItems as &$item) {
            $item['authorize'] = $this->getContactAuthorize($item['udid']);
            $item['backUpBirthCnt'] = $this->getBirthCntDetail($item['id']);
        }
        return $detailItems;
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