<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/5/10
 * Time: 下午5:51
 */
require __DIR__ .'/../vendor/autoload.php';
include __DIR__."/../util/UtilSqlTool.php";
include __DIR__.'/../common/Common.php';
class BirthdayRegisterQuery extends Common
{
    use UtilSqlTool;
    
    public $connectObj;

    public function __construct(Common $common)
    {
        $this->connectObj = $common;
    }

    public function getPointDayBirthdayUserCnt($table = 0, $productSk = 1002)
    {
        $currentCount = 0;
        $currentDate = new \DateTime();
        $currentDate->modify('-1 day');
        $currentYear = intval($currentDate->format('Y'));
        $currentMonth = intval($currentDate->format('m'));
        $currentDay = intval($currentDate->format('d'));
        $queryResult = $this->currentBirthdayTable($table, $currentMonth, $currentDay, 0);
        $queryClass = $this->getClassDevice($queryResult);
        foreach ($queryClass as $key => $value) {
            $currentCount += $this->getProductSk($key, $value, $productSk);
        }
        $solarDate = new \Octinn\Lib\Date\SolarDate($currentYear, $currentMonth, $currentDay);
        $lunarDate = $solarDate->toLunarDate();
        $lunarMonth = $lunarDate->getMonth();
        $lunarDay = $lunarDate->getDay();
        $queryLunarResult = $this->currentBirthdayTable($table, $lunarMonth, $lunarDay, 1);
        $queryLunarClass = $this->getClassDevice($queryLunarResult);
        foreach ($queryLunarClass as $key => $value) {
            $currentCount += $this->getProductSk($key, $value, $productSk);
        }
        echo "currentTable " . $table . " Summation :" . $currentCount . "\n";
        return $currentCount;
    }

    public function getClassDevice($queryResult = array())
    {
        $queryDevice = array();
        foreach ($queryResult as $queryItem) {
            $device = $queryItem['device'];
            if ($device == 0) {
                $queryDevice[0][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 1) {
                $queryDevice[1][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 2) {
                $queryDevice[2][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 3) {
                $queryDevice[3][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 4) {
                $queryDevice[4][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 5) {
                $queryDevice[5][] = "'" . $queryItem['udid'] . "'";
            } elseif ($device == 6) {
                $queryDevice[6][] = "'" . $queryItem['udid'] . "'";
            } else {
                $queryDevice[7][] = "'" . $queryItem['udid'] . "'";
            }
        }
        return $queryDevice;
    }

    public function getProductSk($table = 0, $udids = array(), $productSk = 1002)
    {
        $udidList = implode(',', $udids);
        $currentTableName = 'oistatistics.st_devices_' . $table;
//        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s WHERE s.udid IN ( " . $udidList . " ) AND s.product_sk = " . $productSk;
//        echo $query . " \n";
        $query = $this->getQueryUdidsLinkProductSk($currentTableName, $udidList, $productSk);
        $result = $this->connectObj->fetchCnt($query);
        return $result['cnt'];
    }

    public function getPointDayBrandBirthdayUserCnt($table = 0)
    {
        $currentBrandListCnt = array(
            'iphone_cnt' => 0,
            'xiaomi_cnt' =>0,
            'meizu_cnt' => 0,
            'huawei_cnt' => 0,
            'vivo_cnt' => 0,
            'samsung_cnt' => 0,
            'oppo_cnt' => 0,
            'zte_cnt' => 0
        );
        $currentLunarBrandListCnt = array(
            'iphone_cnt' => 0,
            'xiaomi_cnt' =>0,
            'meizu_cnt' => 0,
            'huawei_cnt' => 0,
            'vivo_cnt' => 0,
            'samsung_cnt' => 0,
            'oppo_cnt' => 0,
            'zte_cnt' => 0
        );
        $currentDate = new \DateTime();
        $currentDate->modify('-1 day');
        $currentYear = intval($currentDate->format('Y'));
        $currentMonth = intval($currentDate->format('m'));
        $currentDay = intval($currentDate->format('d'));
        $queryResult = $this->currentBirthdayTable($table, $currentMonth, $currentDay, 0);
        $queryClass = $this->getClassDevice($queryResult);
        foreach ($queryClass as $key => $value) {
            $deviceBrandList =  $this->fetchBrandsListCount($key, $value);
            $currentBrandListCnt = $this->summationDeviceCnt($currentBrandListCnt, $deviceBrandList);
        }
        $solarDate = new \Octinn\Lib\Date\SolarDate($currentYear, $currentMonth, $currentDay);
        $lunarDate = $solarDate->toLunarDate();
        $lunarMonth = $lunarDate->getMonth();
        $lunarDay = $lunarDate->getDay();
        $queryLunarResult = $this->currentBirthdayTable($table, $lunarMonth, $lunarDay, 1);
        $queryLunarClass = $this->getClassDevice($queryLunarResult);
        foreach ($queryLunarClass as $key => $value) {
            $deviceLunarBrandList = $this->fetchBrandsListCount($key, $value);
            $currentLunarBrandListCnt = $this->summationDeviceCnt($currentLunarBrandListCnt, $deviceLunarBrandList);
        }

        $brandList = $this->summationDeviceCnt($currentBrandListCnt, $currentLunarBrandListCnt);
        echo "== xiaomi_cnt: " . $brandList['xiaomi_cnt'] . " == meizu_cnt: " . $brandList['meizu_cnt'] . " == huawei_cnt: " . $brandList['huawei_cnt'] .
            " == vivo_cnt: " . $brandList['vivo_cnt'] . " == samsung_cnt: " . $brandList['samsung_cnt'] . " == oppo_cnt: " . $brandList['oppo_cnt'] . 
            " == zte_cnt: " . $brandList['zte_cnt'] . " \n";
        return $brandList;
    }
    
    public function summationDeviceCnt($currentBrands = array(), $summationBrands = array())
    {
        $currentBrands['iphone_cnt'] += $summationBrands['iphone_cnt'];
        $currentBrands['xiaomi_cnt'] += $summationBrands['xiaomi_cnt'];
        $currentBrands['meizu_cnt'] += $summationBrands['meizu_cnt'];
        $currentBrands['huawei_cnt'] += $summationBrands['huawei_cnt'];
        $currentBrands['vivo_cnt'] += $summationBrands['vivo_cnt'];
        $currentBrands['samsung_cnt'] += $summationBrands['samsung_cnt'];
        $currentBrands['oppo_cnt'] += $summationBrands['oppo_cnt'];
        $currentBrands['zte_cnt'] += $summationBrands['zte_cnt'];
        return $currentBrands;
    }
    
    public function fetchBrandsListCount($table = 0, $udids = array())
    {
        $iphoneCnt = $this->getBrandCount($table, $udids, array(355)); //iphone brand_sk = 355
        $xiaomiCnt = $this->getBrandCount($table, $udids, array(3)); //iphone brand_sk = 355
        $meizuCnt = $this->getBrandCount($table, $udids, array(9)); //meizu brand_sk = 9
        $huaweiCnt = $this->getBrandCount($table, $udids, array(3397, 19)); // huawei brand_sk = 19 honor brand_sk = 3397
        $vivoArray = $this->fuckVivo();
        $vivoCnt = $this->getfuckVivoModelCount($table, $udids, $vivoArray); // vivo is bitch
        $samsungCnt = $this->getBrandCount($table, $udids, array(4)); // samsung brand_sk = 4
        $oppoCnt = $this->getBrandCount($table, $udids, array(18)); // oppo brand_sk = 18
        $zteCnt = $this->getBrandCount($table, $udids, array(17));  // zte中兴 brand_sk = 14
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

    public function fuckVivo()
    {
        $vivoArray = array();
//        $query = "SELECT model_sk  FROM `oistatistics`.`st_dim_model` WHERE `model_name` LIKE '%vivo%'";
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
//        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s 
//        LEFT JOIN oistatistics.st_dim_model AS m ON s.model_sk = m.model_sk 
//        WHERE s.udid IN ( ". $udidsList . " ) AND m.model_sk IN ( " . $modelsList . ")";
        $query = $this->getQueryUdidLinkModel($currentTableName, $udidsList, $modelsList);
        $brandCount = $this->connectObj->fetchCnt($query);
        return $brandCount['cnt'];
    }

    public function getBrandCount($table = 0, $udids = array(), $brands = array())
    {
        $udidsList = implode(',', $udids);
        $brandList = implode(',', $brands);
        $currentTableName = 'oistatistics.st_devices_' . $table;
//        $query = "SELECT COUNT(*) AS cnt FROM " . $currentTableName ." AS s 
//        LEFT JOIN oistatistics.st_dim_brand AS b ON s.brand_sk = b.brand_sk 
//        WHERE s.udid IN ( ". $udidsList . " ) AND b.brand_sk IN ( " . $brandList . ")";
        $query = $this->getQueryUdidLinkBrand($currentTableName, $udidsList, $brandList);
        $brandCount = $this->connectObj->fetchCnt($query);
        return $brandCount['cnt'];
    }

    public function currentBirthdayTable($table = 0, $birthM = 0, $birthD = 0, $isBirthLunar = 0)
    {
        echo "===== start table: " . $table . " \n";
        $currentTableName = "oibirthday.br_birthdays_" . $table;
//        $sql = sprintf("
//        SELECT bcn.userid,u.udid,(CONV(LEFT(u.udid, 1), 16, 10) DIV 2) AS device FROM %s AS bcn LEFT JOIN oibirthday.users AS u ON bcn.userid = u.id
//        WHERE
//		 	bcn.`birth_is_lunar` = %d AND bcn.birth_m = %d AND bcn.birth_d = %d AND u.udid != '' AND TO_DAYS(u.visit_on) = TO_DAYS('2016-05-15')
//		 GROUP BY
//		 	bcn.birth_m, bcn.birth_d, bcn.userid
//	",
//            $currentTableName,
//            $isBirthLunar,
//            $birthM, $birthD
//        );
        $sql = sprintf("
        SELECT bcn.userid,u.udid,(CONV(LEFT(u.udid, 1), 16, 10) DIV 2) AS device FROM %s AS bcn LEFT JOIN oibirthday.users AS u ON bcn.userid = u.id
        WHERE
		 	bcn.`birth_is_lunar` = %d AND bcn.birth_m = %d AND bcn.birth_d = %d AND u.udid != ''
		 GROUP BY
		 	bcn.birth_m, bcn.birth_d, bcn.userid
	",
            $currentTableName,
            $isBirthLunar,
            $birthM, $birthD
        );
//        $query = $this->getQueryUserItemsOnLunarAndMonthAndDay($currentTableName, $isBirthLunar, $birthM, $birthD);
        $query = $this->connectObj->fetchAssoc($sql);
        return $query;
    }

    public function getMaxUserId()
    {
        $query = $this->getQueryMaxUserId();
        $query = $this->connectObj->fetchCnt($query);
        echo " MAXUsersId: " . $query['id'] . " \n";
        return $query['id'];
    }
}