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

    public function fetchBrandsCnt($userDeviceItems = array())
    {
        $currentBrandListCnt = $this->brandItemInit();
        foreach ($userDeviceItems as $key => $value) {
            $deviceBrandList = $this->getBrandListCnt($key, $value);
            $currentBrandListCnt = $this->summationDeviceCnt($currentBrandListCnt, $deviceBrandList);
        }
        return $currentBrandListCnt;
    }

    public function getBrandListCnt($table, $udids)
    {
        $iphoneCnt = $this->queryBrandCnt($table, $udids, array(355)); //iphone brand_sk = 355
        $xiaomiCnt = $this->queryBrandCnt($table, $udids, array(3)); //iphone brand_sk = 355
        $meizuCnt = $this->queryBrandCnt($table, $udids, array(9)); //meizu brand_sk = 9
        $huaweiCnt = $this->queryBrandCnt($table, $udids, array(3397, 19)); // huawei brand_sk = 19 honor brand_sk = 3397
        $vivoArray = $this->getFuckBrand('vivo');
        $vivoCnt = $this->queryModelCnt($table, $udids, $vivoArray); // vivo is bitch
        $samsungCnt = $this->queryBrandCnt($table, $udids, array(4)); // samsung brand_sk = 4
        $oppoCnt = $this->queryBrandCnt($table, $udids, array(18)); // oppo brand_sk = 18
        $zteCnt = $this->queryBrandCnt($table, $udids, array(17));  // zte中兴 brand_sk = 14
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

    public function queryBrandCnt($table, $udids = array(), $brands = array())
    {
        $udidsList = implode(',', $udids);
        $brandList = implode(',', $brands);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $query = $this->getQueryUdidLinkBrand($currentTableName, $udidsList, $brandList);
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

    public function queryModelCnt($table, $udids = array(), $models = array())
    {
        $udidsList = implode(',', $udids);
        $modelsList = implode(',', $models);
        $currentTableName = 'oistatistics.st_devices_' . $table;
        $modelsCount = $this->connectObj->fetchCnt($this->getQueryUdidLinkModel($currentTableName, $udidsList, $modelsList));
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
}