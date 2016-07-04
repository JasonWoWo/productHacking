<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/7/1
 * Time: 下午4:32
 */

require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\RegisterDetailRetain;

class RegisterRetainPCStat extends RegisterDetailRetain
{
    const PLATFORM_REGISTER_STAT = 'platform_register_statis';
    const CHANNEL_REGISTER_STAT = 'channel_register_statis';

    public function insertPlatformRegister($extendStamp = 0)
    {
        $time = empty($extendStamp) ? time() : $extendStamp;
        $dateTime = new \DateTime(date('Y-m-d', $time));
        $params['create_on'] = $dateTime->format('Y-m-d');
        $this->getCurrentRegisters($dateTime->getTimestamp());
        $platformDetail = $this->getPlatformRegisters();
        foreach ($platformDetail as $appid => $productCnt) {
            $params['app_id'] = $appid;
            $params['product_cnt'] = $productCnt;
            $this->insertCore(self::PLATFORM_REGISTER_STAT, $params);
        }
    }
    
    public function insertChannelRegister($extendStamp = 0) 
    {
        $time = empty($extendStamp) ? time() : $extendStamp;
        $dateTime = new \DateTime(date('Y-m-d', $time));
        $params['create_on'] = $dateTime->format('Y-m-d');
        $this->getCurrentRegisters($dateTime->getTimestamp());
        $channelDetail = $this->getChannelRegisters();
        foreach ($channelDetail as $channel => $channelCnt) {
            $params['channel_id'] = $channel;
            $params['channel_cnt'] = $channelCnt;
            $this->insertCore(self::CHANNEL_REGISTER_STAT, $params);
        }
    }
    
    public function insertCore($table, $params)
    {
        $insertSql = $this->connectObj->insertParamsQuery($table, $params);
        $result = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($result) {
            echo "==== " . $params['create_on'] . " Device Authorize : " . $table . " Success ! \n";
        }
    }
}
$registerPCRetain = new RegisterRetainPCStat();
$registerPCRetain->insertPlatformRegister();
$registerPCRetain->insertChannelRegister();