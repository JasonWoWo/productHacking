<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/7
 * Time: 下午5:04
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\RegisterDetailRetain;

class UserWeeklyRegisterOnPC extends RegisterDetailRetain
{
    const USER_TABLE_PLATFORM_NAME = 'platform_weekly_register_statis';
    const USER_TABLE_CHANNEL_NAME = 'channel_weekly_register_statis';
    
    public function insertPlatformWeeklyRegisters($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $params['create_on'] = "'{$dateTime->format('Y-m-d')}'";
        $platformWeeklyRegisters = $this->getPlatformRegisters();
        foreach (array_keys($platformWeeklyRegisters) as $appId) {
            $params['app_id'] = $appId;
            $params['product_cnt'] = $platformWeeklyRegisters[$appId];
            $this->insertCore(self::USER_TABLE_PLATFORM_NAME, $params);
        }
    }

    public function baseRegistersSource($extendStamp = 0)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify('-1 day');
        $loginEndStamp = $dateTime->getTimestamp() - 6 * self::DEFAULT_TIMESTAMP;
        $this->getCurrentRegisters($dateTime->getTimestamp(), $loginEndStamp);
    }

    public function insertChannelWeeklyRegisters($extendTimeStamp = 0)
    {
        $timeStamp = empty($extendTimeStamp) ? time() : $extendTimeStamp;
        $dateTime = new \DateTime(date('Y-m-d', $timeStamp));
        $dateTime->modify("-1 day");
        $params['create_on'] = "'{$dateTime->format('Y-m-d')}'";
        $channelWeeklyRegisters = $this->getChannelRegisters();
        foreach (array_keys($channelWeeklyRegisters) as $channel) {
            $params['channel_id'] = $channel;
            $params['channel_cnt'] = $channelWeeklyRegisters[$channel];
            $this->insertCore(self::USER_TABLE_CHANNEL_NAME, $params);
        }
    }
}
$weekly = new UserWeeklyRegisterOnPC();
$weekly->baseRegistersSource();
$weekly->insertChannelWeeklyRegisters();
$weekly->insertPlatformWeeklyRegisters();