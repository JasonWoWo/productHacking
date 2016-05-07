<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/6
 * Time: 下午6:42
 */
include __DIR__.'/../common/Common.php';
class Authorize
{
    public $connectObj;

    public $deviceInfo = array();

    public $device = array();

    public function __construct(Common $common)
    {
        $this->connectObj = $common;
    }

    public function fetch_new_device($currentTime = 0)
    {
        $defaultTableNumber = 0;
        $resultList = array();
        while ($defaultTableNumber < 8) {
            $result = $this->currentDevice($defaultTableNumber, $currentTime);
            $resultList[] = $result;
            $defaultTableNumber += 1;
        }
        $this->deviceInfo = $resultList;
    }

    public function currentDevice($table, $currentStamp = 0)
    {
        $currentTable = 'oistatistics.st_devices_' . $table;
        $dayString = "TO_DAYS('" . date('Y-m-d', $currentStamp) . "')";
        $querySql = sprintf("
        SELECT s.udid, s.birthcnt, s.product_sk, v.version_code , b.brand_name, m.model_name
               FROM %s AS s 
               LEFT JOIN oistatistics.st_dim_brand AS b ON b.brand_sk = s.brand_sk
               LEFT JOIN oistatistics.st_dim_model AS m ON m.model_sk = s.model_sk
               LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
               LEFT JOIN oistatistics.st_dim_version AS v ON v.version_sk = s.version_sk 
               WHERE TO_DAYS(d.datevalue) = %s", $currentTable, $dayString);
        echo $querySql . " \n";
        $result = $this->connectObj->fetchAssoc($querySql);
        return $result;
    }

    public function getMongoAuthorize()
    {
        $collection = $this->connectObj->fetchDeviceInfoCollection();
        foreach ($this->deviceInfo as &$listItem) {
            foreach ($listItem as &$item) {
                $query = array('_id' => $item['udid']);
                $list = $collection->findOne($query);
                $item['authorize'] = -1;
                if ($list) {
                    $item['authorize'] = $list['combineAuthorizeStatus'];
                }
                $item['uid'] = isset($list['uid']) ? $list['uid'] : 0;
            }
        }
    }


    public function getAuthorize()
    {
        $list = array(
            'on' => array(),
            'off' => array(),
            'unkown' => array(),
            'uncatch' => array(),
        );
        foreach ($this->deviceInfo as $listItem) {
            foreach ($listItem as $item) {
                if (in_array($item['authorize'], array(1, 9, 5))) {
                    $list['on'][] = $item;
                } elseif (in_array($item['authorize'], array(2, 6, 10))) {
                    $list['off'][] = $item;
                } elseif ($item['authorize'] == 0) {
                    $list['unkown'][] = $item;
                } elseif ($item['authorize'] == -1) {
                    $list['uncatch'][] = $item;
                }
            }
        }
        $this->device = $list;
    }

    public function printData()
    {
        echo "------------udid--------- userid ----- create_on ---- brandName ---- modelName -------- birthcnt ----- product_sk ----- version_code---- authorize \n";
        if (!empty($this->device['on'])) {
            $onList = $this->splitData($this->device['on']);
            echo sprintf(" 开启: 0条: %d , 1条: %d ,  2-5条 %d , 6-10条 %d , 11条 %d \n", $onList['zone'],$onList['one'],$onList['two'],$onList['six'],$onList['ten']);

        }

        if (!empty($this->device['off'])) {
            $onList = $this->splitData($this->device['off']);
            echo sprintf(" 关闭: 0条: %d , 1条: %d ,  2-5条: %d , 6-10条: %d , 11条: %d \n", $onList['zone'],$onList['one'],$onList['two'],$onList['six'],$onList['ten']);

        }
        if (!empty($this->device['unkown'])) {
            $onList = $this->splitData($this->device['unkown']);
            echo sprintf(" 未知: 0条: %d , 1条: %d ,  2-5条: %d , 6-10条: %d , 11条: %d \n", $onList['zone'],$onList['one'],$onList['two'],$onList['six'],$onList['ten']);

        }
        if (!empty($this->device['uncatch'])) {
            //var_dump($this->device['uncatch']);
            $onList = $this->splitData($this->device['uncatch']);
            echo sprintf(" uncatch: 0条: %d , 1条: %d ,  2-5条: %d , 6-10条: %d , 11条: %d \n", $onList['zone'],$onList['one'],$onList['two'],$onList['six'],$onList['ten']);
        }

    }

    public function splitData($authorize = array())
    {
        $deviceOne = 0;
        $deviceZone = 0;
        $deviceTwo = 0;
        $deviceSix = 0;
        $deviceTen = 0;
        foreach ($authorize as $item) {
            $createOn = date('Y-m-d');
            $userId = $item['uid'];
            if ($userId != -1 or $userId != 0) {
                $createOn = $this->getUserCreateOn($userId);
            }
            echo $item['udid'] . ";" . $userId . ";" . $createOn . ";" . $item['brand_name']. ";" .
                $item['model_name'] .";" . $item['birthcnt'] . ";" . $item['product_sk'] . ";" .
                $item['version_code'] . ";" . $item['authorize'] . " \n";
        }
        return array(
            'zone' => $deviceZone,
            'one' => $deviceOne,
            'two' => $deviceTwo,
            'six' => $deviceSix,
            'ten' => $deviceTen
        );
    }

    public function getUserCreateOn($userId = 0)
    {
        $querySql = "SELECT DATE_FORMAT(u.create_on, '%Y-%m-%d') AS create_on FROM oibirthday.users AS u WHERE u.id =" . intval($userId);
        $result = $this->connectObj->fetchCnt($querySql);
        return $result['create_on'];
    }
    
}