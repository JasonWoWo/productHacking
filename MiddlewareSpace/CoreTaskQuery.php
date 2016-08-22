<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/4/29
 * Time: 下午12:50
 */
namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;

class CoreTaskQuery
{
    
    use UtilSqlTool;
    public $connectObj;
    
    public function __construct()
    {
        $this->connectObj = new Common();
    }

    // 获取当日新增的device设备数量
    public function getCurrentCoreAddUdid($extendStamp = 0, $products = array(1001, 1002, 1003))
    {
        $defaultTable = 0;
        $addUdidTotal = 0;
        while ($defaultTable < 8) {
            $addUdidTotal += $this->queryAddUdid($defaultTable, $extendStamp, $products);
            $defaultTable = $defaultTable + 1;
        }
        echo "====== currentDate: " . date('Y-m-d', $extendStamp) . " addUdidTotal: " . $addUdidTotal . " \n";
        return $addUdidTotal;
    }

    public function queryAddUdid($table, $loginString, $products = array())
    {
        $freshDeviceSql = $this->getQueryFreshDeviceSql($table, $loginString, $products);
        $query = $this->connectObj->fetchCnt($freshDeviceSql);
        return $query['cnt'];
    }

    // 获取当日新增的device设备中注册的用户数据(!!! 这个数据  < 当日新增的device设备数量)
    public function getCurrentCoreAddUdidActivists($extendStamp = 0, $products = array(1001, 1002, 1003))
    {
        $defaultTable = 0;
        $addUdidActivistsTotal = 0;
        while ($defaultTable < 8) {
            $addUdidActivistsTotal += $this->queryAddUdidActivists($defaultTable, $extendStamp, $products);
            $defaultTable = $defaultTable + 1;
        }
        echo "=====" . date('Y-m-d', $extendStamp) . " CurrentCoreAddUdidActivists: " . $addUdidActivistsTotal . " \n";
        return $addUdidActivistsTotal;
    }
    
    public function queryAddUdidActivists($table, $loginStamp, $products = array())
    {
        $freshDeviceFreshRegisterSql = $this->getQueryFreshDeviceFreshRegisterSql($table, $loginStamp, $products);
        $result = $this->connectObj->fetchCnt($freshDeviceFreshRegisterSql);
        return $result['cnt'];
    }

    public function getCurrentDevicesCoreTaskCount($extendStamp = 0)
    {
        $defaultTable = 0;
        $dateSkQuery = $this->getStaticsDimDate($extendStamp);
        $dateResult = $this->connectObj->fetchCnt($dateSkQuery);
        $dateSk = $dateResult['date_sk'];
        $currentDevicesCoreTaskCount = 0;
        while ($defaultTable < 8) {
            $tableName = "oistatistics.st_devices_" . $defaultTable;
            $query = $this->getQueryAddDevicesCoreTaskCount($tableName, $dateSk);
            $result = $this->connectObj->fetchCnt($query);
            $currentDevicesCoreTaskCount += $result['cnt'];
            $defaultTable ++;
        }
        return $currentDevicesCoreTaskCount;
    }

    // 当日新增的device设备中已登陆的老用户
    public function getCurrentNewUdidOldUsers($extendStamp = 0, $products = array(1001, 1002, 1003))
    {
        $defaultTable = 0;
        $newUdidOldUsers = 0;
        while ($defaultTable < 8) {
            $newUdidOldUsers += $this->queryNewUdidOldUsers($defaultTable, $extendStamp, $products);
            $defaultTable = $defaultTable + 1;
        }
        echo "======" . date('Y-m-d', $extendStamp) . " FreshDevicesOnSeniorRegisters: " . $newUdidOldUsers . " \n";
        return $newUdidOldUsers;
    }

    public function queryNewUdidOldUsers($table = 0, $loginStamp, $products = array())
    {
        $freshDeviceSeniorRegisterSql = $this->getQueryFreshDeviceSeniorRegisterSql($table, $loginStamp, $products);
        $result = $this->connectObj->fetchAssoc($freshDeviceSeniorRegisterSql);
        return count($result);
    }

    // 获取当日新增的注册用户
    public function fetchCurrentAddUsers($timestamp = 0)
    {
        $dateString = date('Y-m-d', $timestamp);
        $toDaysString = "TO_DAYS('" . $dateString ."')";
        $querySql = sprintf(" SELECT COUNT(u.id) AS cnt FROM oibirthday.users AS u WHERE u.appid IN (1001, 1002, 1003) AND TO_DAYS(u.create_on) = %s", $toDaysString);
        $query = $this->connectObj->fetchCnt($querySql);
        return $query['cnt'];
    }

    public function totalDeviceRetain($currentDateStamp = 0, $isRetain =0, $minCycle = 0)
    {
        $defaultTable = 0;
        $retainCTCnt = 0;
        $retainUnCTCnt = 0;
        while ($defaultTable < 8) {
            $currentCounter = $this->fetchRangeBirthCntOnSchedule($defaultTable, $currentDateStamp, $isRetain, $minCycle);
            $retainUnCTCnt += $currentCounter['rank_0000'] + $currentCounter['rank_0001'] + $currentCounter['rank_0205'];
            $retainCTCnt += $currentCounter['rank_0610'] + $currentCounter['rank_1000'];
            $defaultTable += 1;
        }
        return array(
            'retainCTCnt' => $retainCTCnt,
            'retainUnCTCnt' => $retainUnCTCnt,
        );
    }

    // 获取新增设备的注册用户在不同生日条数的数量
    public function fetchBirthLevelUsers($currentDateStamp = 0, $isRetain = 0, $forward = 0)
    {
        $defaultTable = 0;
        $rankZone = 0;
        $rankOne = 0;
        $rankFive = 0;
        $rankTen = 0;
        $rankMore = 0;
        while ($defaultTable < 8) {
            $currentCounter = $this->fetchRangeBirthCntOnSchedule($defaultTable, $currentDateStamp, $isRetain, $forward, true);
            $rankZone += $currentCounter['rank_0000'];
            $rankOne += $currentCounter['rank_0001'];
            $rankFive += $currentCounter['rank_0205'];
            $rankTen += $currentCounter['rank_0610'];
            $rankMore += $currentCounter['rank_1000'];

            $defaultTable += 1;
        }
        $birthLevelCntList = array(
            'birth_cnt_rank_0000' => $rankZone,
            'birth_cnt_rank_0001' => $rankOne,
            'birth_cnt_rank_0205' => $rankFive,
            'birth_cnt_rank_0610' => $rankTen,
            'birth_cnt_rank_1000' => $rankMore,
        );
        return $birthLevelCntList;
    }

    public function fetchRangeBirthCntOnSchedule($table, $currentDateStamp, $isRetain = 0, $forward = 0, $isInject = false)
    {
        
        $currentTable = 'oistatistics.st_devices_' . $table;
        $loginDate = date('Y-m-d', $currentDateStamp);
        $loginEndString = "TO_DAYS('" . $loginDate ."')";
        $loginStartDate = $this->calculateLoginIn($currentDateStamp, $isRetain);
        $loginStartString = "TO_DAYS(" . $loginStartDate .")";
        $userRank = $this->getRankUserId($currentTable, $loginStartString, $loginEndString);
        $joinBlock = $this->joinBlock($userRank['maxId'], $userRank['minId']);

        $querySql = sprintf("
        SELECT 
	COUNT(*) AS cnt, 
	u.id AS uid, 
	u.udid, 
	u.appid,
	s.birthcnt,
	u.create_on
FROM 
	oibirthday.users AS u 
	%s
	LEFT JOIN %s AS s ON u.udid = s.udid 
	LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
WHERE 
	TO_DAYS(d.datevalue) >= %s AND TO_DAYS(d.datevalue) <= %s
	AND TO_DAYS(u.create_on) >= %s AND TO_DAYS(u.create_on) <= %s
	AND u.appid IN (1001, 1002, 1003)
GROUP BY 
	u.id
        ", $joinBlock, $currentTable, $loginStartString, $loginEndString, $loginStartString, $loginEndString);
        echo $querySql . " \n";
        $result = $this->connectObj->fetchAssoc($querySql);
        foreach ($result as &$item) {
            if ($item['cnt'] == 1) {
                $item['max_bct'] = $item['birthcnt'];
            } elseif ($item['cnt'] > 1 && $item['cnt'] >= $item['birthcnt']) {
                $item['max_bct'] = $item['cnt'];
            } elseif ($item['cnt'] < $item['birthcnt']) {
                $item['max_bct'] = $item['birthcnt'];
            }
//            if ($isInject) {
//                $this->connectObj->injectMongo($item, $forward);
//            }
        }
        $rankCounter = $this->getRankCounter($result);
        return $rankCounter;
    }

    public function getRankUserId($tableName, $loginStartString, $loginEndString)
    {
        $userRankSql = sprintf("
SELECT MAX(u.id) AS maxId, MIN(u.id) AS minId 
FROM oibirthday.users AS u 
LEFT JOIN %s AS s ON u.udid = s.udid
LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
WHERE TO_DAYS(d.datevalue) >= %s AND TO_DAYS(d.datevalue) <= %s
AND TO_DAYS(u.create_on) >= %s AND TO_DAYS(u.create_on) <= %s", $tableName, $loginStartString, $loginEndString, $loginStartString, $loginEndString);
        echo $userRankSql . " \n";
        $query = $this->connectObj->fetchCnt($userRankSql);
        return $query;
    }

    public function joinBlock($maxUserId, $minUserId)
    {
        $birthdayTableMax = $this->get_number_birthday_number($maxUserId);
        $birthdayTableMin = $this->get_number_birthday_number($minUserId);
        $joinBlock = sprintf(" LEFT JOIN oibirthday.br_birthdays_" . $birthdayTableMax ." AS bcm ON bcm.userid = u.id ");
        while ($birthdayTableMin < $birthdayTableMax) {
            $name = "bci_" . $birthdayTableMin;
            $joinBlock .= sprintf(" LEFT JOIN oibirthday.br_birthdays_" . $birthdayTableMin . " AS ". $name ." ON " . $name .".userid = u.id ");
            $birthdayTableMin += 1;
        }
        return $joinBlock;
    }
    
    

    public function getRankCounter($resultItems = array())
    {
        $rankZone = 0;
        $rankOne = 0;
        $rankFive = 0;
        $rankTen = 0;
        $rankMore = 0;

        foreach ($resultItems as $item) {
            if ($item['appid'] == 1003) {
                continue;
            }
            if ($item['max_bct'] <= 0) {
                $rankZone += 1;
            } elseif ($item['max_bct'] ==  1) {
                $rankOne += 1;
            } elseif ($item['max_bct'] >= 2 && $item['max_bct'] <= 5) {
                $rankFive += 1;
            } elseif ($item['max_bct'] >= 6 && $item['max_bct'] <= 10) {
                $rankTen += 1;
            } else {
                $rankMore += 1;
            }
        }

        return array(
            'rank_0000' => $rankZone,
            'rank_0001' => $rankOne,
            'rank_0205' => $rankFive,
            'rank_0610' => $rankTen,
            'rank_1000' => $rankMore,
        );
    }

    public function getFullDeviceSummation($loginStamp = 0)
    {
        $defaultTable = 0;
        $summation = 0;
        while ($defaultTable < 8) {
            $summation += $this->fetchDailySummation($defaultTable, $loginStamp);
            $defaultTable += 1;
        }
        echo "====== QUERY " . date('Y-m-d', $loginStamp) . " Daily Summation: " . $summation . " ====== \n";
        return $summation;
    }

    public function fetchDailySummation($table = 0, $loginInStamp = 0)
    {
        $currentTable = 'oistatistics.st_devices_' . $table;
        $currentDateString = "TO_DAYS('" . date('Y-m-d') . "')";
        $loginDateString = "TO_DAYS('" . date('Y-m-d', $loginInStamp) . "')";
        $querySql = sprintf("
SELECT COUNT(s.udid) AS summation 
FROM %s AS s 
LEFT JOIN oibirthday.users AS u ON s.udid = u.udid
LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
WHERE TO_DAYS(u.create_on) >= %s AND TO_DAYS(u.create_on) <= %s AND TO_DAYS(d.datevalue) = %s", $currentTable, $loginDateString, $currentDateString, $loginDateString);
        $result = $this->connectObj->fetchCnt($querySql);
        return $result['summation'];
    }

    public function checkCurrentDateData($tableName, $dayString)
    {
        $sql = sprintf("
        SELECT COUNT(*) AS cnt
        FROM %s AS ups
        WHERE ups.`create_on` = %s",
            $tableName,
            $dayString
        );
        $query = $this->connectObj->fetchCnt($sql);
        if ($query['cnt']) {
            return true;
        }
        return false;
    }

    public function getRegisterCompleteCount($extendStamp = 0)
    {
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $query = $this->getQueryRegistersCompleteInfo($timestamp);
        $result = $this->connectObj->fetchCnt($query);
        return $result['cnt'];
    }

    public function calculateLoginIn($currentStamp = 0, $isRetain = 0)
    {
        $loginInStamp = $currentStamp - $isRetain * 86400;
        $loginInString = "'" . date('Y-m-d', $loginInStamp) . "'";
        return $loginInString;
    }

    public function get_number_birthday_number($userId)
    {
        $table_num = floor( intval( $userId ) / 50000 );
        return $table_num;
    }
}