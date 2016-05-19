<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/17
 * Time: 下午3:06
 */
require __DIR__ . '/Bootstrap.php';
use MiddlewareSpace\AppListQuery;

class AppListBrand extends AppListQuery
{
    public function getBrandListCount()
    {
        $appKey = $this->getAppPackage();
        $userItems = $this->fetchAppList();
        foreach ($userItems as $key => $value) {
            $brandList = $this->fetchBrandCount($value);
            echo  $appKey[$key] . "== xiaomi_cnt: " . $brandList['xiaomi_cnt'] . " == meizu_cnt: " . $brandList['meizu_cnt'] . " == huawei_cnt: " . $brandList['huawei_cnt'] .
                " == vivo_cnt: " . $brandList['vivo_cnt'] . " == samsung_cnt: " . $brandList['samsung_cnt'] . " == oppo_cnt: " . $brandList['oppo_cnt'] .
                " == zte_cnt: " . $brandList['zte_cnt'] . " \n";
        }
        echo "==== Summation". $this->getUserCount() . " \n";
    }
    
    public function getBrand()
    {
        $userItems = $this->fetchAppList();
        $brandList = $this->fetchBrandCount($userItems);
        echo  "== xiaomi_cnt: " . $brandList['xiaomi_cnt'] . " == meizu_cnt: " . $brandList['meizu_cnt'] . " == huawei_cnt: " . $brandList['huawei_cnt'] .
            " == vivo_cnt: " . $brandList['vivo_cnt'] . " == samsung_cnt: " . $brandList['samsung_cnt'] . " == oppo_cnt: " . $brandList['oppo_cnt'] .
            " == zte_cnt: " . $brandList['zte_cnt'] . " \n";
    }
}
$appList = new AppListBrand();
//$appList->getBrandListCount();
$appList->getBrand();