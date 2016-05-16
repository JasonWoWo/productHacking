<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/8
 * Time: 下午2:45
 */
include __DIR__."/../util/UtilTool.php";
include __DIR__."/../util/UtilSqlTool.php";
include __DIR__.'/../common/Common.php';
class UserRegisterQuery
{
    use UtilTool;
    use UtilSqlTool;
    public $connectObj;

    public function __construct(Common $common)
    {
        $this->connectObj = $common;
    }

    /**
     * @param int $currentStamp
     * @param int $isRetain
     * @param bool $isCount
     * @return array
     */
    public function getCurrentRankRegisterCnt($currentStamp = 0, $isRetain = 0, $isCount = true)
    {
        $tableName = 'oibirthday.users';
        $currentDate = date('Y-m-d', $currentStamp);
        $loginEndDate = $this->connectObj->calculateLoginIn($currentStamp);
        $loginEndString = "TO_DAYS(" . $loginEndDate . ")";
        $loginStartDate = $loginEndDate;
        if ($isRetain) {
            $loginStartDate = $this->connectObj->calculateLoginIn($currentStamp, $isRetain - 1);   // 7  =>  6
        }
        $loginStartString = "TO_DAYS(" . $loginStartDate . ")";
        if ($isCount) {
            $count = $this->fetchSelectQueryCnt($tableName, $loginStartString, $loginEndString);
            return array(
                'create_on' => "'" . $currentDate . "'",
                'user_rank_cnt' => $count,
            );
        }

        $userItems = $this->fetchSelectQueryList($tableName, $loginStartString, $loginEndString);
        $userDeviceItems = $this->getClassDevice($userItems);
        return $userDeviceItems;
    }

    public function fetchBrandsCnt($userDeviceItems = array(), $timeStamp = 0)
    {
        $currentBrandListCnt = $this->brandItemInit();
        foreach ($userDeviceItems as $key => $value) {
            $deviceBrandList = $this->getBrandListCnt($key, $value, $timeStamp);
            $currentBrandListCnt = $this->summationDeviceCnt($currentBrandListCnt, $deviceBrandList);
        }
        return $currentBrandListCnt;
    }

    public function getBrandListCnt($table, $udids, $timeStamp = 0)
    {
        $iphoneCnt = $this->queryBrandCnt($table, $udids, array(355), $timeStamp); //iphone brand_sk = 355
        $xiaomiCnt = $this->queryBrandCnt($table, $udids, array(3), $timeStamp); //iphone brand_sk = 355
        $meizuCnt = $this->queryBrandCnt($table, $udids, array(9), $timeStamp); //meizu brand_sk = 9
        $huaweiCnt = $this->queryBrandCnt($table, $udids, array(3397, 19), $timeStamp); // huawei brand_sk = 19 honor brand_sk = 3397
        $vivoArray = $this->getFuckBrand('vivo');
        $vivoCnt = $this->queryModelCnt($table, $udids, $vivoArray, $timeStamp); // vivo is bitch
        $samsungCnt = $this->queryBrandCnt($table, $udids, array(4), $timeStamp); // samsung brand_sk = 4
        $oppoCnt = $this->queryBrandCnt($table, $udids, array(18), $timeStamp); // oppo brand_sk = 18
        $zteCnt = $this->queryBrandCnt($table, $udids, array(17), $timeStamp);  // zte中兴 brand_sk = 14
        return array(
            'iphone_cnt' => $iphoneCnt,
            'xiaomi_cnt' => $xiaomiCnt,
            'meizu_cnt' => $meizuCnt,
            'huawei_cnt' => $huaweiCnt,
            'vivo_cnt' => $vivoCnt,
            'samsung_cnt' => $samsungCnt,
            'oppo_cnt' => $oppoCnt,
            'zte_cnt' => $zteCnt
        );
    }

    public function queryBrandCnt($table, $udids = array(), $brands = array(), $timeStamp = 0)
    {
        $udidsList = implode(',', $udids);
        $brandList = implode(',', $brands);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $query = $this->getQueryLoginUdidLinkBrand($currentTableName, $udidsList, $brandList, $timeStamp);
        $brandCount = $this->connectObj->fetchCnt($query);
        return $brandCount['cnt'];
    }

    public function getFuckBrand($model = 'vivo')
    {
        $vivoArray = array();
        $result = $this->connectObj->fetchAssoc($this->getQueryVivoFuck($model));
        foreach ($result as $item) {
            $vivoArray[] = $item['model_sk'];
        }
        return $vivoArray;
    }

    public function queryModelCnt($table, $udids = array(), $models = array(), $timeStamp = 0)
    {
        $udidsList = implode(',', $udids);
        $modelsList = implode(',', $models);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $modelsCount = $this->connectObj->fetchCnt($this->getQueryLoginUdidLinkModel($currentTableName, $udidsList, $modelsList, $timeStamp));
        return $modelsCount['cnt'];
    }
    
    public function fetchSelectQueryCnt($tableName, $loginStartString, $loginEndString)
    {
        $sql = sprintf("
        SELECT 
	COUNT(*) AS cnt 
FROM 
	%s AS u 
WHERE 
	TO_DAYS(u.create_on) >= %s 
	AND TO_DAYS(u.create_on) <= %s
	",
            $tableName,
            $loginStartString,
            $loginEndString);
        $query = $this->connectObj->fetchCnt($sql);
        return $query['cnt'];
    }

    public function fetchSelectQueryList($tableName, $loginStartString, $loginEndString)
    {
        $sql = sprintf("
        SELECT 
	u.udid, u.id , (CONV(LEFT(u.udid, 1), 16, 10) DIV 2) AS device
FROM 
	%s AS u 
WHERE 
	TO_DAYS(u.create_on) >= %s 
	AND TO_DAYS(u.create_on) <= %s
	",
            $tableName,
            $loginStartString,
            $loginEndString);
        $results = $this->connectObj->fetchAssoc($sql);
        return $results;
    }

    public function fetchProductListCnt($userDeviceItems = array(), $productSk = 1002, $timeStamp = 0)
    {
        $productCnt = 0;
        foreach ($userDeviceItems as $key => $value) {
            $productCnt += $this->getProductSk($key, $value, $productSk, $timeStamp);
        }
        return $productSk;
    }
    
    public function getProductSk($table = 0, $udids = array(), $productSk = 1002, $timeStamp = 0)
    {
        $udidList = implode(',', $udids);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $query = $this->getQueryLoginUdidsLinkProductSk($currentTableName, $udidList, $productSk, $timeStamp);
        $result = $this->connectObj->fetchCnt($query);
        return $result['cnt'];
    }
}