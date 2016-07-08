<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/4
 * Time: 下午4:08
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\RegisterDetailRetain;

class RegisterOnRetainPCStat extends RegisterDetailRetain
{
    const PLATFORM_REGISTER_STAT = 'platform_register_statis';
    const CHANNEL_REGISTER_STAT = 'channel_register_statis';

    public function updateRetainPlatform($extendStamp = 0)
    {
        $this->retainPlatformRegisters($extendStamp, 2);
        $this->retainPlatformRegisters($extendStamp, 3);
        $this->retainPlatformRegisters($extendStamp, 7);
        $this->retainPlatformRegisters($extendStamp, 15);
        $this->retainPlatformRegisters($extendStamp, 30);
    }

    public function retainPlatformRegisters($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $platformDetailRetain = $this->getPlatformRegistersRetain($timestamp, $isRetain);
        $paramKey = $this->getIsRetainDailyParamsKey($isRetain);
        foreach (array_keys($platformDetailRetain) as $appId) {
            $params = array('id');
            $where = array(
                'create_on' => "'" . date('Y-m-d', $timestamp - self::DEFAULT_TIMESTAMP * $isRetain) ."'",
                'app_id' => $appId,
            );
            $selectQuery = $this->connectObj->selectParamsQuery(self::PLATFORM_REGISTER_STAT, $params, $where);
            $result = $this->connectObj->fetchAssoc($selectQuery);
            if (empty($result)) {
                echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::PLATFORM_REGISTER_STAT . " \n";
                return ;
            }
            $this->updateSqlCore(self::PLATFORM_REGISTER_STAT, array($paramKey => $platformDetailRetain[$appId]), $where, $isRetain);
        }

    }

    public function updateRetainChannel($extendStamp = 0)
    {
        $this->retainChannelRegisters($extendStamp, 2);
        $this->retainChannelRegisters($extendStamp, 3);
        $this->retainChannelRegisters($extendStamp, 7);
        $this->retainChannelRegisters($extendStamp, 15);
        $this->retainChannelRegisters($extendStamp, 30);
    }

    public function retainChannelRegisters($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $channelDetailCnt = $this->getChannelRegistersRetain($timestamp, $isRetain);
        $paramKey = $this->getIsRetainDailyParamsKey($isRetain);
        foreach (array_keys($channelDetailCnt) as $channel) {
            $params = array('id');
            $where = array(
                'create_on' => "'" . date('Y-m-d', $timestamp - self::DEFAULT_TIMESTAMP * $isRetain) ."'",
                'channel_id' => $channel,
            );
            $selectQuery = $this->connectObj->selectParamsQuery(self::CHANNEL_REGISTER_STAT, $params, $where);
            $result = $this->connectObj->fetchAssoc($selectQuery);
            if (empty($result)) {
                echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::CHANNEL_REGISTER_STAT . " \n";
                return ;
            }
            $this->updateSqlCore(self::CHANNEL_REGISTER_STAT, array($paramKey => $channelDetailCnt[$channel]), $where, $isRetain);
        }
    }
}
$retainDetail = new RegisterOnRetainPCStat();
$retainDetail->updateRetainPlatform();
$retainDetail->updateRetainChannel();