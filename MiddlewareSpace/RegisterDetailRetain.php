<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/4
 * Time: 下午2:57
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class RegisterDetailRetain
{
    use UtilSqlTool;
    use UtilTool;

    const DEFAULT_TIMESTAMP = 86400;

    public $registersDetails = array();

    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getCurrentRegisters($loginStartStamp = 0, $loginEndStamp = 0)
    {
        $registersQuery = $this->getQueryRegisterByCreateOn($loginStartStamp, $loginEndStamp);
        $registers = $this->connectObj->fetchAssoc($registersQuery);
        if ($registers) {
            $this->registersDetails = $registers;
        }
    }

    public function getPlatformRegisters()
    {
        $platformDetailCnt = $this->getProductPlatform();
        foreach ($this->registersDetails as $item) {
            $appid = $item['appid'];
            if (array_key_exists($appid, $platformDetailCnt)) {
                $platformDetailCnt[$appid] += 1;
            }
        }
        return $platformDetailCnt;
    }

    public function getChannelRegisters()
    {
        $channelDetailCnt = $this->getProductChannel();
        foreach ($this->registersDetails as $item) {
            $channel = $item['chnid'];
            if (array_key_exists($channel, $channelDetailCnt)) {
                $channelDetailCnt[$channel] += 1;
            }
        }
        return $channelDetailCnt;
    }

    public function getPlatformRegistersRetain($currentStamp = 0, $isRetain = 0, $minCycle = 0)
    {
        $platformDetailRetain = $this->getProductPlatform();
        list($loginStartStamp, $loginEndStamp, $visitStartStamp, $visitEndStamp) = $this->fetchTimestamp($currentStamp, $isRetain, $minCycle);
        foreach (array_keys($platformDetailRetain) as $appId) {
            $registerRetainQuery = $this->getQueryRegisterRetainByVisitOn($loginStartStamp, $loginEndStamp, $visitStartStamp, $visitEndStamp, array(), array($appId));
            $appIdRegisters = $this->connectObj->fetchCnt($registerRetainQuery);
            $platformDetailRetain[$appId] += $appIdRegisters['cnt'];
        }
        return $platformDetailRetain;
    }
    
    public function getChannelRegistersRetain($currentStamp = 0, $isRetain = 0, $minCycle = 0) 
    {
        $channelDetailCnt = $this->getChannelRegisters();
        list($loginStartStamp, $loginEndStamp, $visitStartStamp, $visitEndStamp) = $this->fetchTimestamp($currentStamp, $isRetain, $minCycle);
        foreach (array_keys($channelDetailCnt) as $channel) {
            $registerRetainQuery = $this->getQueryRegisterRetainByVisitOn($loginStartStamp, $loginEndStamp, $visitStartStamp, $visitEndStamp, array($channel));
            $channelRegisters = $this->connectObj->fetchCnt($registerRetainQuery);
            $channelDetailCnt[$channel] += $channelRegisters['cnt'];
        }
        return $channelDetailCnt;
    }
    
    public function fetchTimestamp($currentStamp = 0, $isRetain = 0, $minCycle = 0)
    {
        $currentDate = date('Y-m-d', $currentStamp);
        $visitDate = new \DateTime($currentDate);
        $visitDate->modify('-1 day');
        $loginEndStamp = $currentStamp - $isRetain * self::DEFAULT_TIMESTAMP;
        $rank = $isRetain + $minCycle;
        $loginStartStamp = $currentStamp - $rank * self::DEFAULT_TIMESTAMP;
        $visitStartStamp = $visitDate->getTimestamp();
        if ($minCycle) {
            $loginEndStamp = $visitDate->getTimestamp() - $isRetain * self::DEFAULT_TIMESTAMP;
            $visitStartStamp = $currentStamp - $minCycle * self::DEFAULT_TIMESTAMP;
        }
        $visitEndStamp = $visitDate->getTimestamp();
        return array($loginStartStamp, $loginEndStamp, $visitStartStamp, $visitEndStamp);
    }

    public function insertCore($table, $params)
    {
        $insertSql = $this->connectObj->insertParamsQuery($table, $params);
        $result = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($result) {
            echo "==== " . $params['create_on'] . " insert : " . $table . " Success ! \n";
        }
    }
    
    public function updateSqlCore($table, $params = array(), $where = array(), $isRetain = 0)
    {
        $updateQuery = $this->connectObj->updateParamsQuery($table, $params, $where);
        $query = $this->connectObj->fetchCakeStatQuery($updateQuery);
        if ($query) {
            echo " === " . $where['create_on'] . " week isRetain : " . $isRetain . " success !!! \n";
        }
    }
}