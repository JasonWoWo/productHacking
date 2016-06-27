<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/6/24
 * Time: 下午5:43
 */

require __DIR__ . '/Bootstrap.php';
use MiddlewareSpace\Authorize;

class EntireDeviceAuthorize extends Authorize
{
    const DEVICE_AUTHORIZE = 'device_authorize_stat';
    
    public function insertEntireAuthorize()
    {
        $current = new \DateTime();
        $this->fetch_new_device($current->modify('-1 day')->getTimestamp());
        $this->getMongoAuthorize();
        $params = array();
        $paramList = $this->getPlatformCntList();
        if (empty($paramList)) {
            echo "==== UnCatch Authorize On Open Please Check! \n";
            return;
        }
        array_walk($paramList, function ($value, $key) use (&$params) { $params[$key] = $value;});
        $params['create_on'] = "'". $current->format('Y-m-d') ."'";
        
        $this->getAuthorize();
        $platformOn = $this->getPlatformAuthorizeOn();
        array_walk($platformOn, function ($value, $key) use (&$params) {$params[$key] = $value;});
        $insertSql = $this->connectObj->insertParamsQuery(self::DEVICE_AUTHORIZE, $params);
        $result = $this->connectObj->fetchCakeStatQuery($insertSql);
        if ($result) {
            echo "==== " . $params['create_on'] . " Device Authorize : " . self::DEVICE_AUTHORIZE . " Success ! \n"; 
        }
    }
}
$entireDevice = new EntireDeviceAuthorize();
$entireDevice->insertEntireAuthorize();