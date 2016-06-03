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
    
    public $registerCoreTaskDevices = array();

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    // 获取当日新增的device设备数量
    public function getCurrentCoreAddUdid($extendStamp = 0)
    {
        $defaultTable = 0;
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = date('Y-m-d', $timestamp);
        $dayString = "TO_DAYS('" . $currentDate . "')";
        $addUdidTotal = 0;
        while ($defaultTable < 8) {
            $addUdidTotal += $this->queryAddUdid($defaultTable, $dayString);
            $defaultTable = $defaultTable + 1;
        }
        echo "====== currentDate: " . $currentDate . " addUdidTotal: " . $addUdidTotal . " \n";
        return $addUdidTotal;
    }

    public function queryAddUdid($table, $loginString)
    {
        $tableName = "oistatistics.st_devices_" . $table;
        $sql = sprintf("
SELECT 
	COUNT(s.udid) AS cnt
FROM 
	%s AS s 
	LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
WHERE 
	TO_DAYS(d.datevalue) = %s",
            $tableName,
            $loginString
        );
        $query = $this->connectObj->fetchCnt($sql);
        return $query['cnt'];
    }

    // 获取当日新增的device设备中注册的用户数据(!!! 这个数据  < 当日新增的device设备数量)
    public function getCurrentCoreAddUdidActivists($extendStamp = 0)
    {
        $defaultTable = 0;
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = date('Y-m-d', $timestamp);
        $dayString = "TO_DAYS('" . $currentDate . "')";
        $addUdidActivistsTotal = 0;
        while ($defaultTable < 8) {
            $addUdidActivistsTotal += $this->queryAddUdidActivists($defaultTable, $dayString);
            $defaultTable = $defaultTable + 1;
        }
        echo "=====" . $currentDate . " CurrentCoreAddUdidActivists: " . $addUdidActivistsTotal . " \n";
        return $addUdidActivistsTotal;
    }
    
    public function queryAddUdidActivists($table, $dayString)
    {
        $tableName = "oistatistics.st_devices_" . $table;
        $sql = sprintf("
        SELECT 
	COUNT(s.udid) AS cnt
FROM 
	oibirthday.users AS u 
    LEFT JOIN %s AS s ON s.udid = u.udid
	LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk 
WHERE 
	TO_DAYS(d.datevalue) = %s
    AND TO_DAYS(u.create_on) = %s
    ",
            $tableName,
            $dayString,
            $dayString
        );

        $query = $this->connectObj->fetchCnt($sql);
        return $query['cnt'];
    }

    public function getCurrentDevicesCoreTaskCount($extendStamp = 0)
    {
        $defaultTable = 0;
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDevicesCoreTaskCount = 0;
        $udidLists = implode(',', $this->registerCoreTaskDevices);
        while ($defaultTable < 8) {
            $tableName = "oistatistics.st_devices_" . $defaultTable;
            $query = $this->getQueryAddDevicesCoreTaskCount($tableName, $udidLists, $timestamp);
            $result = $this->connectObj->fetchCnt($query);
            $currentDevicesCoreTaskCount += $result['cnt'];
            $defaultTable ++;
        }
        return $currentDevicesCoreTaskCount;
    }

    // 当日新增的device设备中已登陆的老用户
    public function getCurrentNewUdidOldUsers($extendStamp = 0)
    {
        $defaultTable = 0;
        $timestamp = empty($extendStamp) ? time() : $extendStamp;
        $currentDate = date('Y-m-d', $timestamp);
        $dayString = "TO_DAYS('" . $currentDate . "')";
        $newUdidOldUsers = 0;
        while ($defaultTable < 8) {
            $newUdidOldUsers += $this->queryNewUdidOldUsers($defaultTable, $dayString);
            $defaultTable = $defaultTable + 1;
        }
        echo "======" . $currentDate . " CurrentNewUdidOldUsers: " . $newUdidOldUsers . " \n";
        return $newUdidOldUsers;
    }

    public function queryNewUdidOldUsers($table = 0, $dayString)
    {
        $tableName = "oistatistics.st_devices_" . $table;
        $sql = sprintf("
        SELECT 
	u.udid
FROM 
	%s AS s 
	LEFT JOIN oibirthday.users AS u ON s.udid = u.udid 
	LEFT JOIN oistatistics.st_dim_date AS d ON s.create_date_sk = d.date_sk  
WHERE 
	TO_DAYS(d.datevalue) = %s
    AND TO_DAYS(u.create_on) < TO_DAYS(u.visit_on)
    AND TO_DAYS(u.visit_on) = %s
        ",
            $tableName,
            $dayString,
            $dayString
        );
        $query = $this->connectObj->fetchAssoc($sql);
        foreach ($query as $item) {
            $this->registerCoreTaskDevices[] = "'" . $item['udid'] . "'";
        }
        return count($query);
    }

    // 获取当日新增的注册用户
    public function fetchCurrentAddUsers($timestamp = 0)
    {
        $dateString = date('Y-m-d', $timestamp);
        $toDaysString = "TO_DAYS('" . $dateString ."')";
        $querySql = sprintf(" SELECT COUNT(u.id) AS cnt FROM oibirthday.users AS u WHERE TO_DAYS(u.create_on) = %s", $toDaysString);
        $query = $this->connectObj->fetchCnt($querySql);
        return $query['cnt'];
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
            $currentCounter = $this->fetchRangeBirthCntOnSchedule($defaultTable, $currentDateStamp, $isRetain, $forward);
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

    public function fetchRangeBirthCntOnSchedule($table, $currentDateStamp, $isRetain = 0, $forward = 0)
    {
        
        $currentTable = 'oistatistics.st_devices_' . $table;
        $loginDate = date('Y-m-d', $currentDateStamp);
        $loginEndString = "TO_DAYS('" . $loginDate ."')";
        $loginStartDate = $this->calculateLoginIn($currentDateStamp, $isRetain);
        $loginStartString = "TO_DAYS(" . $loginStartDate .")";
        // $userRank['maxId'], $userRank['minId']
        $userRank = $this->getRankUserId($currentTable, $loginStartString, $loginEndString);
        $joinBlock = $this->joinBlock($userRank['maxId'], $userRank['minId']);

        $querySql = sprintf("
        SELECT 
	COUNT(*) AS cnt, 
	u.id AS uid, 
	u.udid, 
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
            $this->connectObj->injectMongo($item, $forward);
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
            if ($item['max_bct'] <= 0) {
                $rankZone += 1;
            } elseif ($item['max_bct'] ==  1) {
                $rankOne += 1;
            } elseif ($item['max_bct'] >= 2 && $item['max_bct'] <= 5) {
                $rankFive += 1;
            } elseif ($item['max_bct'] >= 6 && $item['max_bct'] <= 10) {
                $this->registerCoreTaskDevices[] = "'" . $item['udid'] . "'";
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