<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/16
 * Time: 下午2:40
 */

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class RegisterWechatAuthorize
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;

    /**
     * @var \DateTime
     */
    public $currentDate;

    public function __construct()
    {
        $this->connectObj = new Common();
        $this->currentDate = new \DateTime();
        $this->currentDate->modify('-1 day');
    }

    // 每天新增设备中的当日新增注册用户 绑定
    public function fetchUserBindWechat()
    {
        $query = $this->getQueryRegisterBindWeChat($this->currentDate->getTimestamp());
        $userItems = $this->connectObj->fetchAssoc($query);
        return $userItems;
    }

    // 每天新增设备中的当日新增注册用户 绑定且关注
    public function fetchUserBindAndFocusWechat()
    {
        $query = $this->getQueryRegisterBindAndFocusOnWeChat($this->currentDate->getTimestamp());
        $userItems = $this->connectObj->fetchAssoc($query);
        return $userItems;
    }
    
    // 获取指定日期分平台的 绑定 和 绑定和关注数据
    public function fetchProductListCnt($productItem = 1001)
    {
        $productParams = $this->getProductSkParamsInit($productItem);
        $productKey = $this->getMappingParamsKey($productItem);
        $userBindItems = $this->fetchUserBindWechat();
        $deviceBindItems = $this->getClassDevice($userBindItems);
        foreach ($deviceBindItems as $key => $value) {
            $productParams[$productKey[0]] += $this->getProductSk($key, $value, $productItem);
        }

        $userBindFocusItems = $this->fetchUserBindAndFocusWechat();
        $deviceBindFocusItems = $this->getClassDevice($userBindFocusItems);
        foreach ($deviceBindFocusItems as $key => $value) {
            $productParams[$productKey[1]] += $this->getProductSk($key, $value, $productItem);
        }
        return $productParams;
    }

    // 获取指定日期的绑定数据 和 绑定且关注的数据
    public function fetchBrandsBindRelateRatio($isBindFocus = false)
    {
        $brandItems = $this->brandItemInit();
        $userItems = $this->fetchUserBindWechat();
        if ($isBindFocus) {
            $userItems = $this->fetchUserBindAndFocusWechat();
        }
        $deviceItems = $this->getClassDevice($userItems);
        foreach ($deviceItems as $key => $value) {
            $deviceBrandItems = $this->fetchBrandsItemsCnt($key, $value);
            $brandItems = $this->summationDeviceCnt($brandItems, $deviceBrandItems);
        }
        return $brandItems;
    }
    
    public function fetchBrandsItemsCnt($table = 0, $udids = array())
    {
        $xiaomiCnt = $this->getBrandCount($table, $udids, array(3)); //iphone brand_sk = 355
        $meizuCnt = $this->getBrandCount($table, $udids, array(9)); //meizu brand_sk = 9
        $huaweiCnt = $this->getBrandCount($table, $udids, array(3397, 19)); // huawei brand_sk = 19 honor brand_sk = 3397
        $vivoArray = $this->fuckVivo();
        $vivoCnt = $this->getfuckVivoModelCount($table, $udids, $vivoArray); // vivo is bitch
        $samsungCnt = $this->getBrandCount($table, $udids, array(4)); // samsung brand_sk = 4
        $oppoCnt = $this->getBrandCount($table, $udids, array(18)); // oppo brand_sk = 18
        $zteCnt = $this->getBrandCount($table, $udids, array(17));  // zte中兴 brand_sk = 14
        return array(
            'iphone_cnt' => 0,
            'xiaomi_cnt' => $xiaomiCnt,
            'meizu_cnt' => $meizuCnt,
            'huawei_cnt' => $huaweiCnt,
            'vivo_cnt' => $vivoCnt,
            'samsung_cnt' => $samsungCnt,
            'oppo_cnt' => $oppoCnt,
            'zte_cnt' => $zteCnt
        );
    }

    public function getBrandCount($table = 0, $udids = array(), $brands = array())
    {
        $udidsList = implode(',', $udids);
        $brandList = implode(',', $brands);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $query = $this->getQueryLoginUdidLinkBrand($currentTableName, $udidsList, $brandList, $this->currentDate->getTimestamp());
        $brandCount = $this->connectObj->fetchCnt($query);
        return $brandCount['cnt'];
    }

    public function fuckVivo()
    {
        $vivoArray = array();
        $query = $this->getQueryVivoFuck();
        $result = $this->connectObj->fetchAssoc($query);
        foreach ($result as $item) {
            $vivoArray[] = $item['model_sk'];
        }
        return $vivoArray;
    }

    public function getfuckVivoModelCount($table = 0, $udids = array(), $models = array())
    {
        $udidsList = implode(',', $udids);
        $modelsList = implode(',', $models);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $query = $this->getQueryLoginUdidLinkModel($currentTableName, $udidsList, $modelsList, $this->currentDate->getTimestamp());
        $brandCount = $this->connectObj->fetchCnt($query);
        return $brandCount['cnt'];
    }

    public function getProductSk($table = 0, $udids = array(), $productSk = 1002)
    {
        $udidList = implode(',', $udids);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $query = $this->getQueryLoginUdidsLinkProductSk($currentTableName, $udidList, $productSk, $this->currentDate->getTimestamp());
        $result = $this->connectObj->fetchCnt($query);
        return $result['cnt'];
    }

}