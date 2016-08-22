<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/22
 * Time: 下午4:19
 */
require __DIR__ . '/Bootstrap.php';

use MiddlewareSpace\CoreTaskQuery;
class RegisterCoreTaskOnPlatform extends CoreTaskQuery
{
    const PLATFORM_REGISTER_CORE_TASK_STAT = 'platform_user_core_task_stat';
    
    const PLATFORM_ITEMS = array(1001, 1002);
    
    public function productDetail($extendStamp = 0, $product = array())
    {
        // 获取$product平台的新增设备的新注册用户
        $freshRegisters = $this->getCurrentCoreAddUdidActivists($extendStamp, $product);
        // 获取$product平台的新增设备的数
        $freshDevices = $this->getCurrentCoreAddUdid($extendStamp, $product);
        // 获取$product平台的新增设备的老用户数
        $freshDeviceSeniorRegisters = $this->getCurrentNewUdidOldUsers($extendStamp, $product);
        return array(
            'fresh_device_freshers' => $freshRegisters,
            'fresh_device' => $freshDevices,
            'fresh_device_seniors' => $freshDeviceSeniorRegisters,
        );
    }
    
    public function insertProductItemsDetail($extendStamp = 0)
    {
        $timeStamp = empty($extendStamp) ? time() : $extendStamp;
        $insertParams = array();
        $insertParams['create_on'] = sprintf("'%s'", date('Y-m-d'));
        $productOnPhoneParams = $this->productDetail($timeStamp, array(1001));
        $insertParams['iphone_fresh_device_freshers'] = $productOnPhoneParams['fresh_device_freshers'];
        $insertParams['iphone_fresh_device'] = $productOnPhoneParams['fresh_device'];
        $insertParams['iphone_fresh_device_seniors'] = $productOnPhoneParams['fresh_device_seniors'];
        $productOnAndroidParams = $this->productDetail($timeStamp, array(1002));
        $insertParams['android_fresh_device_freshers'] = $productOnAndroidParams['fresh_device_freshers'];
        $insertParams['android_fresh_device'] = $productOnAndroidParams['fresh_device'];
        $insertParams['android_fresh_device_seniors'] = $productOnAndroidParams['fresh_device_seniors'];
        $insertQuery = $this->connectObj->insertParamsQuery(self::PLATFORM_REGISTER_CORE_TASK_STAT, $insertParams);
        $query = $this->connectObj->fetchCakeStatQuery($insertQuery);
        if ($query) {
            echo "Product On " . $insertParams['create_on'] . " Insert Into " . self::PLATFORM_REGISTER_CORE_TASK_STAT . "Success ! \n";
        }
    }
    
}
$product = new RegisterCoreTaskOnPlatform();
$product->insertProductItemsDetail();