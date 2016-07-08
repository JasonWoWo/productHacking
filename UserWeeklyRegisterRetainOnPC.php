<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/7
 * Time: 下午5:05
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\RegisterDetailRetain;
class UserWeeklyRegisterRetainOnPC extends RegisterDetailRetain
{
    const USER_TABLE_PLATFORM_NAME = 'platform_weekly_register_statis';
    const USER_TABLE_CHANNEL_NAME = 'channel_weekly_register_statis';

    public function channelMainRetain($extendStamp = 0)
    {
        $this->updateBaseUserChannelRetainCnt($extendStamp, 7);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 14);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 21);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 28);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 35);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 42);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 49);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 56);
        $this->updateBaseUserChannelRetainCnt($extendStamp, 63);
    }

    public function updateBaseUserChannelRetainCnt($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $nextDate = $this->fetchNextTime($timestamp);
        $channelRetainItems = $this->getChannelRegistersRetain($nextDate->getTimestamp(), $isRetain, 7);
        $paramKey = $this->getIsRetainWeekParamsKey($isRetain);
        $distance = $isRetain + 1;
        $loginIn = $nextDate->modify("-$distance days")->format('Y-m-d');
        $loginInString = "'{$loginIn}'";
        foreach (array_keys($channelRetainItems) as $channel) {
            $params = array('id');
            $where = array(
                'create_on' => $loginInString,
                'channel_id' => $channel
            );
            $selectQuery = $this->connectObj->selectParamsQuery(self::USER_TABLE_CHANNEL_NAME, $params, $where);
            $result = $this->connectObj->fetchAssoc($selectQuery);
            if (empty($result)) {
                echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::USER_TABLE_CHANNEL_NAME . " \n";
                return ;
            }
            $this->updateSqlCore(self::USER_TABLE_CHANNEL_NAME, array($paramKey => $channelRetainItems[$channel]), $where);
        }
    }

    public function platformMainRetain($extendStamp = 0)
    {
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 7);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 14);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 21);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 28);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 35);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 42);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 47);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 56);
        $this->updateBaseUserPlatformRetainCnt($extendStamp, 63);
    }

    public function updateBaseUserPlatformRetainCnt($extendStamp = 0, $isRetain = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $nextDate = $this->fetchNextTime($timestamp);
        $platformRetainItems = $this->getPlatformRegistersRetain($nextDate->getTimestamp(), $isRetain, 7);
        $paramKey = $this->getIsRetainWeekParamsKey($isRetain);
        $distance = $isRetain + 1;
        $loginIn = $nextDate->modify("-$distance days")->format('Y-m-d');
        $loginInString = "'{$loginIn}'";
        foreach (array_keys($platformRetainItems) as $appId) {
            $params = array('id');
            $where = array(
                'create_on' => $loginInString,
                'app_id' => $appId,
            );
            $selectQuery = $this->connectObj->selectParamsQuery(self::USER_TABLE_PLATFORM_NAME, $params, $where);
            $result = $this->connectObj->fetchAssoc($selectQuery);
            if (empty($result)) {
                echo "UnCatch the result where create_on :" . $where['create_on'] . "In " . self::USER_TABLE_PLATFORM_NAME . " \n";
                return ;
            }
            $this->updateSqlCore(self::USER_TABLE_PLATFORM_NAME, array($paramKey => $platformRetainItems[$appId]), $where);
        }
    }

    public function fetchNextTime($timestamp = 0)
    {
        $currentDate = new \DateTime(date('Y-m-d', $timestamp));
        $nextDate = clone $currentDate;
        $weekdays = date("N", $currentDate->getTimestamp());
        $nextWeekDistance = 8 - $weekdays;
        $nextDate->modify("+$nextWeekDistance days");
        return $nextDate;
    }

}
$weekPCRetain = new UserWeeklyRegisterRetainOnPC();
$weekPCRetain->platformMainRetain();
$weekPCRetain->channelMainRetain();