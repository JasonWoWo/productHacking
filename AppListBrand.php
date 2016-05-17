<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/17
 * Time: 下午3:06
 */
include __DIR__ . "/middleware/AppListQuery.php";
class AppListBrand extends AppListQuery
{
    public $common;

    public function __construct()
    {
        $this->common = new Common();
        parent::__construct($this->common);
    }
    
    public function getBrandListCount()
    {
        $userItems = $this->fetchAppList();
        $brandList = $this->fetchBrandCount($userItems);
        echo "== xiaomi_cnt: " . $brandList['xiaomi_cnt'] . " == meizu_cnt: " . $brandList['meizu_cnt'] . " == huawei_cnt: " . $brandList['huawei_cnt'] .
            " == vivo_cnt: " . $brandList['vivo_cnt'] . " == samsung_cnt: " . $brandList['samsung_cnt'] . " == oppo_cnt: " . $brandList['oppo_cnt'] .
            " == zte_cnt: " . $brandList['zte_cnt'] . " \n";
    }
}
$appList = new AppListBrand();
$appList->getBrandListCount();