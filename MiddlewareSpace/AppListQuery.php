<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/17
 * Time: 下午3:06
 */
namespace MiddlewareSpace;

use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;
use CommonSpace\Common;

class AppListQuery
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;
    
    public $userCount = 0;

    public $content = array();

    public function __construct()
    {
        $this->connectObj = new Common();
    }
    
    public function fetchAppList()
    {
        $appListCollection = $this->connectObj->fetchAppListCollection();
        $currentDate = new \DateTime();
        $previousDate = clone $currentDate;
        $previousDate->modify('-30 days');
        $query = array('t' => array('$gte' => $previousDate->getTimestamp(), '$lte' => $currentDate->getTimestamp()));
        $results = $appListCollection->find($query);
        foreach ($results as $item) {
//            foreach ($item['applist'] as $itemDetail) {
//                if (in_array($itemDetail['name'], array_keys($this->getAppPackage()))) {
//                    $this->content[$itemDetail['name']][] = $this->getCurrentDeviceNumber($item['_id']);
//                }
//            }
            // 临时逻辑看分母分布
            $this->content[] = $this->getCurrentDeviceNumber($item['_id']);
            $this->userCount += 1;
        }
        return $this->content;
    }

    public function getCurrentDeviceNumber($udid)
    {
        $firstString = substr($udid, 0, 1);
        $number = intval(hexdec($firstString) / 2);
        return array(
            'device' => $number,
            'udid' => $udid
        );
    }
    
    public function getUserCount()
    {
        return $this->userCount;
    }
    
    public function fetchBrandCount($userItems = array())
    {
        $brandItems = $this->brandItemInit();
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
        $query = $this->getQueryUdidLinkBrand($currentTableName, $udidsList, $brandList);
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
        $query = $this->getQueryUdidLinkModel($currentTableName, $udidsList, $modelsList);
        $brandCount = $this->connectObj->fetchCnt($query);
        return $brandCount['cnt'];
    }
}